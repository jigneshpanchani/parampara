<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddSellPaymentRequest;
use App\Http\Requests\Admin\StoreSellRequest;
use App\Http\Requests\Admin\UpdateSellRequest;
use App\Models\Product;
use App\Models\Sell;
use App\Services\SellService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellController extends Controller
{
    public function __construct(
        private SellService $sellService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Sell::with(['items.product', 'cashSellInvoice', 'onlineSellInvoice', 'mixSellInvoice'])
            ->orderBy('sell_date', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('sell_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sell_date', '<=', $request->date_to);
        }
        if ($request->filled('payment_mode') && in_array($request->payment_mode, ['cash', 'upi', 'gpay', 'mix'], true)) {
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

        $sells = $query->paginate(20)->withQueryString();

        return view('admin.sells.index', compact('sells'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::active()->orderByName()->get();

        return view('admin.sells.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSellRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $totalAmount = $this->sellService->calculateTotalAmount(
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $attributes = $this->sellService->buildSellAttributes($validated, $totalAmount);
            $sell = Sell::create($attributes);

            $this->sellService->createSellItems(
                $sell,
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            StockService::deductStockFromSale($sell->items);
        });

        return redirect()->route('admin.sells.index')
            ->with('success', 'Sale recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sell $sell): View
    {
        $sell->load(['items.product', 'sellPayments', 'cashSellInvoice', 'onlineSellInvoice', 'mixSellInvoice']);
        return view('admin.sells.show', compact('sell'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sell $sell): View
    {
        $sell->load(['items.product', 'sellPayments']);
        $existingProductIds = $sell->items->pluck('product_id')->all();

        $products = Product::where(function ($query) use ($existingProductIds) {
            $query->where('is_active', true)
                ->orWhereIn('id', $existingProductIds);
        })->orderByName()->get();

        // Default: use stored values
        $displayPaymentMode  = $sell->payment_mode;
        $displayAmountPaid   = (float) $sell->amount_paid;
        $displayCashAmount   = (float) ($sell->cash_amount ?? 0);
        $displayOnlineAmount = (float) ($sell->online_amount ?? 0);

        // If payments were recorded via the Pay modal, reflect them in the form
        if ($sell->sellPayments->isNotEmpty()) {
            $displayAmountPaid = (float) $sell->total_paid;

            // Only auto-derive payment mode when none was set at sale time
            if ($sell->payment_mode === null) {
                $methods = $sell->sellPayments->pluck('payment_method')->unique()->values();

                if ($methods->count() === 1 && in_array($methods[0], ['cash', 'upi', 'gpay'])) {
                    $displayPaymentMode = $methods[0];
                } elseif ($methods->count() > 1) {
                    $displayPaymentMode  = 'mix';
                    $displayCashAmount   = (float) $sell->sellPayments->where('payment_method', 'cash')->sum('amount');
                    $displayOnlineAmount = (float) $sell->sellPayments->whereIn('payment_method', ['upi', 'gpay', 'bank_transfer', 'cheque', 'other'])->sum('amount');
                }
            }
        }

        return view('admin.sells.edit', compact(
            'sell', 'products',
            'displayPaymentMode', 'displayAmountPaid',
            'displayCashAmount', 'displayOnlineAmount'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSellRequest $request, Sell $sell): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $sell) {
            $totalAmount = $this->sellService->calculateTotalAmount(
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $attributes = $this->sellService->buildSellAttributes($validated, $totalAmount);
            $sell->update($attributes);

            // Sell_payments were pre-filled into amount_paid on the edit form.
            // Delete them so recalculatePaymentStatus() won't double-count.
            $sell->sellPayments()->delete();

            StockService::addStockBackFromSale($sell->items);

            $sell->items()->delete();
            $this->sellService->createSellItems(
                $sell,
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $sell->load('items');
            StockService::deductStockFromSale($sell->items);
        });

        return redirect()->route('admin.sells.index')
            ->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sell $sell): RedirectResponse
    {
        DB::transaction(function () use ($sell) {
            StockService::addStockBackFromSale($sell->items);

            $sell->returns()->delete();
            $sell->items()->delete();
            $sell->delete();
        });

        return redirect()->route('admin.sells.index')
            ->with('success', 'Sale deleted successfully.');
    }

    /**
     * Get payment details for a sell (JSON).
     */
    public function getPaymentDetails(Sell $sell): JsonResponse
    {
        $sell->load('items.product', 'sellPayments');

        $items = $sell->items->map(fn ($item) => [
            'product_name' => $item->product->product_name,
            'quantity' => $item->quantity,
            'selling_price' => number_format($item->selling_price, 2),
            'total_price' => number_format($item->total_price, 2),
        ]);

        $payments = $sell->sellPayments->map(fn ($payment) => [
            'payment_date' => $payment->payment_date->format('d M Y'),
            'amount' => number_format($payment->amount, 2),
            'payment_method' => $payment->getPaymentMethodLabel(),
            'reference_number' => $payment->reference_number ?? '-',
            'notes' => $payment->notes ?? '-',
        ]);

        $totalPaidFromPayments = $sell->getTotalPaidFromPayments();
        $totalPaid = $sell->amount_paid + $totalPaidFromPayments;
        $remaining = max(0, $sell->total_amount - $totalPaid);

        return response()->json([
            'success' => true,
            'sell' => [
                'id' => $sell->id,
                'seller_name' => $sell->seller_name ?? '-',
                'sell_date' => $sell->sell_date->format('d M Y'),
                'total_amount' => number_format($sell->total_amount, 2),
                'initial_paid' => number_format($sell->amount_paid, 2),
                'payments_total' => number_format($totalPaidFromPayments, 2),
                'total_paid' => number_format($totalPaid, 2),
                'remaining_amount' => number_format($remaining, 2),
                'payment_status' => ucfirst($sell->payment_status),
                'payment_mode' => $sell->payment_mode_label ?: '—',
            ],
            'items' => $items,
            'payments' => $payments,
        ]);
    }

    /**
     * Add payment for a sell.
     */
    public function addPayment(AddSellPaymentRequest $request, Sell $sell): JsonResponse
    {
        $validated = $request->validated();

        $sell->sellPayments()->create([
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $sell->recalculatePaymentStatus();
        $sell->refresh();

        $totalPaid = $sell->amount_paid + $sell->getTotalPaidFromPayments();
        $remaining = max(0, $sell->total_amount - $totalPaid);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully.',
            'total_paid' => number_format($totalPaid, 2),
            'remaining_amount' => number_format($remaining, 2),
            'payment_status' => ucfirst($sell->payment_status),
        ]);
    }
}
