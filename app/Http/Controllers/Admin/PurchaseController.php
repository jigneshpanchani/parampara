<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddPurchasePaymentRequest;
use App\Http\Requests\Admin\StorePurchaseRequest;
use App\Http\Requests\Admin\UpdatePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\PurchaseService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $purchases = Purchase::with('items', 'payments')
            ->orderBy('purchase_date', 'desc')
            ->get();

        $totalPurchases = $purchases->sum('total_amount');
        $totalPaid = $purchases->sum(fn ($purchase) => $purchase->getTotalPaidAmount());
        $totalPending = $totalPurchases - $totalPaid;

        return view('admin.purchases.index', compact('purchases', 'totalPurchases', 'totalPaid', 'totalPending'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::orderByName()->get();

        return view('admin.purchases.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $totalAmount = $this->purchaseService->calculateTotalFromProducts(
            $validated['products'],
            (float) ($validated['transportation_cost'] ?? 0)
        );

        $attributes = $this->purchaseService->buildStoreAttributes($validated, $totalAmount);
        $purchase = Purchase::create($attributes);

        $this->purchaseService->createItemsFromProducts($purchase, $validated['products']);

        StockService::addStockFromPurchase($purchase->items);

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase): View
    {
        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase): View
    {
        $products = Product::orderByName()->get();
        $purchase->load('items.product');

        return view('admin.purchases.edit', compact('purchase', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validated();

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

        return redirect()->route('admin.purchases.index')
            ->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase): RedirectResponse
    {
        StockService::removeStockFromPurchase($purchase->items);

        $purchase->items()->delete();
        $purchase->delete();

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
            'payment_date' => $payment->payment_date->format('d M Y'),
            'amount' => number_format($payment->amount, 2),
            'payment_method' => $payment->getPaymentMethodLabel(),
            'payment_status' => ucfirst($payment->payment_status),
            'reference_number' => $payment->reference_number ?? '-',
            'notes' => $payment->notes ?? '-',
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
}
