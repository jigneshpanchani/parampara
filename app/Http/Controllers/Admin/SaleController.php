<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddSalePaymentRequest;
use App\Http\Requests\Admin\StoreSaleRequest;
use App\Http\Requests\Admin\UpdateSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        private SaleService $saleService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Sale::with(['items.product', 'cashSaleInvoice', 'onlineSaleInvoice', 'mixSaleInvoice'])
            ->orderBy('sale_date', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        if ($request->filled('payment_mode') && in_array($request->payment_mode, array_keys(config('payment.sale_modes')), true)) {
            $query->where('payment_mode', $request->payment_mode);
        }
        if ($request->filled('payment_status') && in_array($request->payment_status, ['paid', 'pending', 'partial'], true)) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('seller_search')) {
            $term = trim($request->seller_search);
            $query->where(function ($q) use ($term) {
                $q->where('seller_name', 'like', "%{$term}%")
                  ->orWhere('seller_contact_number', 'like', "%{$term}%");
            });
        }

        $sales = $query->paginate(20)->withQueryString();

        return view('admin.sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::active()->orderByName()->get();

        return view('admin.sales.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $totalAmount = $this->saleService->calculateTotalAmount(
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $attributes = $this->saleService->buildSaleAttributes($validated, $totalAmount);
            // Lock in the "originated as pay-later" flag at entry time. We intentionally
            // do NOT touch this field on update() so the historical fact survives even if
            // the customer pays the balance later (which only changes pending_amount).
            $attributes['is_pay_later'] = ($attributes['amount_paid'] < $totalAmount);
            $sale = Sale::create($attributes);

            $this->saleService->createSaleItems(
                $sale,
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            StockService::deductStockFromSale($sale->items);
        });

        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale): View
    {
        $sale->load(['items.product', 'salePayments', 'cashSaleInvoice', 'onlineSaleInvoice', 'mixSaleInvoice']);
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale): View
    {
        $sale->load(['items.product', 'salePayments']);
        $existingProductIds = $sale->items->pluck('product_id')->all();

        $products = Product::where(function ($query) use ($existingProductIds) {
            $query->where('is_active', true)
                ->orWhereIn('id', $existingProductIds);
        })->orderByName()->get();

        // Default: use stored values
        $displayPaymentMode  = $sale->payment_mode;
        $displayAmountPaid   = (float) $sale->amount_paid;
        $displayCashAmount   = (float) ($sale->cash_amount ?? 0);
        $displayOnlineAmount = (float) ($sale->online_amount ?? 0);

        // If payments were recorded via the Pay modal, reflect them in the form
        if ($sale->salePayments->isNotEmpty()) {
            $displayAmountPaid = (float) $sale->total_paid;

            // Only auto-derive payment mode when none was set at sale time
            if ($sale->payment_mode === null) {
                $methods = $sale->salePayments->pluck('payment_method')->unique()->values();

                $directSaleModes = array_diff(array_keys(config('payment.sale_modes')), ['mix']);
                $onlinePaymentMethods = array_diff(array_keys(config('payment.sale_payment_methods')), ['cash']);
                if ($methods->count() === 1 && in_array($methods[0], $directSaleModes)) {
                    $displayPaymentMode = $methods[0];
                } elseif ($methods->count() > 1) {
                    $displayPaymentMode  = 'mix';
                    $displayCashAmount   = (float) $sale->salePayments->where('payment_method', 'cash')->sum('amount');
                    $displayOnlineAmount = (float) $sale->salePayments->whereIn('payment_method', $onlinePaymentMethods)->sum('amount');
                }
            }
        }

        return view('admin.sales.edit', compact(
            'sale', 'products',
            'displayPaymentMode', 'displayAmountPaid',
            'displayCashAmount', 'displayOnlineAmount'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $sale) {
            $totalAmount = $this->saleService->calculateTotalAmount(
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $attributes = $this->saleService->buildSaleAttributes($validated, $totalAmount);
            $sale->update($attributes);

            // sale_payments were pre-filled into amount_paid on the edit form.
            // Delete them so recalculatePaymentStatus() won't double-count.
            $sale->salePayments()->delete();

            StockService::addStockBackFromSale($sale->items);

            $sale->items()->delete();
            $this->saleService->createSaleItems(
                $sale,
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $sale->load('items');
            StockService::deductStockFromSale($sale->items);
        });

        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale): RedirectResponse
    {
        DB::transaction(function () use ($sale) {
            StockService::addStockBackFromSale($sale->items);

            $sale->returns()->delete();
            $sale->items()->delete();
            $sale->delete();
        });

        return redirect()->route('admin.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    /**
     * Get payment details for a sale (JSON).
     */
    public function getPaymentDetails(Sale $sale): JsonResponse
    {
        $sale->load('items.product', 'salePayments');

        $items = $sale->items->map(fn ($item) => [
            'product_name' => $item->product->product_name,
            'quantity' => $item->quantity,
            'selling_price' => number_format($item->selling_price, 2),
            'total_price' => number_format($item->total_price, 2),
        ]);

        $payments = $sale->salePayments->map(fn ($payment) => [
            'id'                   => $payment->id,
            'payment_date'         => $payment->payment_date->format('d M Y'),
            'payment_date_raw'     => $payment->payment_date->format('Y-m-d'),
            'amount'               => number_format($payment->amount, 2),
            'amount_raw'           => (float) $payment->amount,
            'payment_method'       => $payment->getPaymentMethodLabel(),
            'payment_method_raw'   => $payment->payment_method,
            'reference_number'     => $payment->reference_number ?? '-',
            'reference_number_raw' => $payment->reference_number ?? '',
            'notes'                => $payment->notes ?? '-',
            'notes_raw'            => $payment->notes ?? '',
        ]);

        $totalPaidFromPayments = $sale->getTotalPaidFromPayments();
        $totalPaid = $sale->amount_paid + $totalPaidFromPayments;
        $remaining = max(0, $sale->total_amount - $totalPaid);

        return response()->json([
            'success' => true,
            'sale' => [
                'id' => $sale->id,
                'seller_name' => $sale->seller_name ?? '-',
                'sale_date' => $sale->sale_date->format('d M Y'),
                'total_amount' => number_format($sale->total_amount, 2),
                'initial_paid' => number_format($sale->amount_paid, 2),
                'payments_total' => number_format($totalPaidFromPayments, 2),
                'total_paid' => number_format($totalPaid, 2),
                'remaining_amount' => number_format($remaining, 2),
                'payment_status' => ucfirst($sale->payment_status),
                'payment_mode' => $sale->payment_mode_label ?: '—',
            ],
            'items' => $items,
            'payments' => $payments,
        ]);
    }

    /**
     * Add payment for a sale.
     */
    public function addPayment(AddSalePaymentRequest $request, Sale $sale): JsonResponse
    {
        $validated = $request->validated();

        $sale->salePayments()->create([
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $sale->recalculatePaymentStatus();
        $sale->refresh();

        $totalPaid = $sale->amount_paid + $sale->getTotalPaidFromPayments();
        $remaining = max(0, $sale->total_amount - $totalPaid);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully.',
            'total_paid' => number_format($totalPaid, 2),
            'remaining_amount' => number_format($remaining, 2),
            'payment_status' => ucfirst($sale->payment_status),
        ]);
    }

    /**
     * Update an existing sale payment (all editable fields).
     */
    public function updatePayment(Request $request, Sale $sale, SalePayment $salePayment): JsonResponse
    {
        abort_if($salePayment->sale_id !== $sale->id, 404);

        $allowedMethods = array_keys(config('payment.sale_payment_methods', []));

        // Cap = sale total - initial amount_paid - sum of OTHER follow-up payments.
        $otherPaid = (float) $sale->amount_paid
            + (float) $sale->salePayments()->where('id', '!=', $salePayment->id)->sum('amount');
        $maxAmount = round((float) $sale->total_amount - $otherPaid, 2);

        $validated = $request->validate([
            'payment_date'     => ['required', 'date'],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:' . max($maxAmount, 0.01)],
            'payment_method'   => ['required', 'in:' . implode(',', $allowedMethods)],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.max' => "Amount cannot exceed remaining payable (₹{$maxAmount}).",
        ]);

        $salePayment->update([
            'payment_date'     => $validated['payment_date'],
            'amount'           => $validated['amount'],
            'payment_method'   => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes'            => $validated['notes'] ?? null,
        ]);

        $sale->recalculatePaymentStatus();

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully.',
        ]);
    }
}
