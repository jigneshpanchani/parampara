<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddPurchasePaymentRequest;
use App\Http\Requests\Admin\StorePurchaseRequest;
use App\Http\Requests\Admin\UpdatePurchaseRequest;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\PurchaseService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Purchase::with('items', 'payments')->orderBy('purchase_date', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }
        if ($request->filled('bill_type') && in_array($request->bill_type, ['gst', 'without_gst'], true)) {
            $query->where('bill_type', $request->bill_type);
        }

        $purchases = $query->get();

        if ($request->filled('payment_status') && in_array($request->payment_status, ['paid', 'partial', 'pending'], true)) {
            $purchases = $purchases->filter(function (Purchase $p) use ($request) {
                return $p->getPaymentStatus() === $request->payment_status;
            })->values();
        }

        $totalPurchases = $purchases->sum('total_amount');
        $totalPaid      = $purchases->sum(fn ($p) => $p->getTotalPaidAmount());
        $totalPending   = $totalPurchases - $totalPaid;

        // Split by bill type (within filtered set)
        $gstPurchases    = $purchases->where('bill_type', 'gst');
        $nonGstPurchases = $purchases->where('bill_type', 'without_gst');

        $gstTotal   = $gstPurchases->sum('total_amount');
        $gstPaid    = $gstPurchases->sum(fn ($p) => $p->getTotalPaidAmount());
        $gstPending = $gstTotal - $gstPaid;

        $nonGstTotal   = $nonGstPurchases->sum('total_amount');
        $nonGstPaid    = $nonGstPurchases->sum(fn ($p) => $p->getTotalPaidAmount());
        $nonGstPending = $nonGstTotal - $nonGstPaid;

        return view('admin.purchases.index', compact(
            'purchases',
            'totalPurchases', 'totalPaid', 'totalPending',
            'gstTotal', 'gstPaid', 'gstPending',
            'nonGstTotal', 'nonGstPaid', 'nonGstPending'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::active()->orderByName()->get();

        return view('admin.purchases.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $totalAmount = $this->purchaseService->calculateTotalFromProducts(
                $validated['products'],
                (float) ($validated['transportation_cost'] ?? 0)
            );

            $attributes = $this->purchaseService->buildStoreAttributes($validated, $totalAmount);
            $purchase = Purchase::create($attributes);

            $this->purchaseService->createItemsFromProducts($purchase, $validated['products']);

            StockService::addStockFromPurchase($purchase->items);
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase): View
    {
        $purchase->load('items.product', 'payments');

        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase): View
    {
        $purchase->load('items.product');
        $existingProductIds = $purchase->items->pluck('product_id')->all();

        $products = Product::where(function ($query) use ($existingProductIds) {
            $query->where('is_active', true)
                ->orWhereIn('id', $existingProductIds);
        })->orderByName()->get();

        return view('admin.purchases.edit', compact('purchase', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $purchase) {
            $totalAmount = $this->purchaseService->calculateTotalFromArrays(
                $validated['product_id'],
                $validated['quantity'],
                $validated['purchase_price'],
                (float) ($validated['transportation_cost'] ?? 0)
            );

            $attributes = $this->purchaseService->buildUpdateAttributes($validated, $totalAmount);
            $purchase->update($attributes);

            StockService::removeStockFromPurchase($purchase->items);

            $purchase->items()->delete();
            $this->purchaseService->createItemsFromArrays(
                $purchase,
                $validated['product_id'],
                $validated['quantity'],
                $validated['purchase_price']
            );

            $purchase->load('items');
            StockService::addStockFromPurchase($purchase->items);
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        DB::transaction(function () use ($purchase) {
            StockService::removeStockFromPurchase($purchase->items);

            $purchase->items()->delete();
            $purchase->returns()->delete();
            $purchase->payments()->delete();
            $purchase->delete();
        });

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase deleted successfully.');
    }

    /**
     * Get payment details for a purchase (JSON).
     */
    public function getPaymentDetails(Purchase $purchase): JsonResponse
    {
        $purchase->load('items.product', 'payments');

        $items = $purchase->items->map(fn ($item) => [
            'product_name' => $item->product->product_name,
            'quantity' => $item->quantity,
            'purchase_price' => number_format($item->purchase_price, 2),
            'total_price' => number_format($item->total_price, 2),
        ]);

        $payments = $purchase->payments->map(fn ($payment) => [
            'id'                   => $payment->id,
            'payment_date'         => $payment->payment_date->format('d M Y'),
            'payment_date_raw'     => $payment->payment_date->format('Y-m-d'),
            'amount'               => number_format($payment->amount, 2),
            'amount_raw'           => (float) $payment->amount,
            'payment_method'       => $payment->getPaymentMethodLabel(),
            'payment_method_raw'   => $payment->payment_method,
            'payment_status'       => ucfirst($payment->payment_status),
            'reference_number'     => $payment->reference_number ?? '-',
            'reference_number_raw' => $payment->reference_number ?? '',
            'notes'                => $payment->notes ?? '-',
            'notes_raw'            => $payment->notes ?? '',
        ]);

        return response()->json([
            'success' => true,
            'purchase' => [
                'id' => $purchase->id,
                'supplier_name' => $purchase->supplier_name,
                'purchase_date' => $purchase->purchase_date->format('d M Y'),
                'subtotal' => number_format($purchase->getSubtotal(), 2),
                'transportation_cost' => number_format($purchase->transportation_cost ?? 0, 2),
                'expense' => number_format($purchase->expense ?? 0, 2),
                'total_payable' => number_format($purchase->getTotalPayableAmount(), 2),
                'total_amount' => number_format($purchase->total_amount, 2),
                'total_paid' => number_format($purchase->getTotalPaidAmount(), 2),
                'remaining_amount' => number_format($purchase->getRemainingAmount(), 2),
                'payment_status' => ucfirst($purchase->getPaymentStatus()),
            ],
            'items' => $items,
            'payments' => $payments,
        ]);
    }

    /**
     * Add payment for a purchase.
     */
    public function addPayment(AddPurchasePaymentRequest $request, Purchase $purchase): JsonResponse
    {
        $validated = $request->validated();

        $payment = $purchase->payments()->create([
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'payment_status' => 'paid',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully.',
            'payment' => [
                'payment_date' => $payment->payment_date->format('d M Y'),
                'amount' => number_format($payment->amount, 2),
                'payment_method' => $payment->getPaymentMethodLabel(),
                'payment_status' => ucfirst($payment->payment_status),
                'reference_number' => $payment->reference_number ?? '-',
                'notes' => $payment->notes ?? '-',
            ],
            'total_paid' => number_format($purchase->getTotalPaidAmount(), 2),
            'remaining_amount' => number_format($purchase->getRemainingAmount(), 2),
        ]);
    }

    /**
     * Update an existing payment (all editable fields).
     */
    public function updatePayment(Request $request, Purchase $purchase, Payment $payment): JsonResponse
    {
        abort_if($payment->purchase_id !== $purchase->id, 404);

        $allowedMethods = array_keys(config('payment.purchase_payment_methods', []));

        // Cap = total payable - sum of OTHER payments (so the row's own amount can grow up to remaining + its current value).
        $otherPaid = $purchase->payments()->where('id', '!=', $payment->id)->sum('amount');
        $maxAmount = round($purchase->getTotalPayableAmount() - $otherPaid, 2);

        $validated = $request->validate([
            'payment_date'     => ['required', 'date'],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:' . max($maxAmount, 0.01)],
            'payment_method'   => ['required', 'in:' . implode(',', $allowedMethods)],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ], [
            'amount.max' => "Amount cannot exceed remaining payable (₹{$maxAmount}).",
        ]);

        $payment->update([
            'payment_date'     => $validated['payment_date'],
            'amount'           => $validated['amount'],
            'payment_method'   => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'notes'            => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment updated successfully.',
        ]);
    }
}
