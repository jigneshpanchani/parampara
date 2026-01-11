<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all purchases sorted by date descending
        $purchases = Purchase::with('items', 'payments')
            ->orderBy('purchase_date', 'desc')
            ->get();

        // Calculate dashboard statistics
        $totalPurchases = $purchases->sum('total_amount');
        $totalPaid = $purchases->sum(function ($purchase) {
            return $purchase->getTotalPaidAmount();
        });
        $totalPending = $totalPurchases - $totalPaid;

        return view('admin.purchases.index', compact('purchases', 'totalPurchases', 'totalPaid', 'totalPending'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('admin.purchases.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'supplier_name' => 'required|string|max:255',
            'bill_details' => 'nullable|string',
            'transportation_cost' => 'nullable|numeric|min:0',
            'bill_due_date' => 'nullable|date',
            'expense' => 'nullable|numeric|min:0',
            'expense_details' => 'nullable|string',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.purchase_price' => 'required|numeric|min:0',
        ]);

        $totalAmount = 0;
        foreach ($validated['products'] as $product) {
            $totalAmount += $product['quantity'] * $product['purchase_price'];
        }
        $totalAmount += $validated['transportation_cost'] ?? 0;

        $purchase = Purchase::create([
            'purchase_date' => $validated['purchase_date'],
            'supplier_name' => $validated['supplier_name'],
            'bill_details' => $validated['bill_details'],
            'transportation_cost' => $validated['transportation_cost'] ?? 0,
            'bill_due_date' => $validated['bill_due_date'],
            'total_amount' => $totalAmount,
            'expense' => $validated['expense'] ?? 0,
            'expense_details' => $validated['expense_details'],
        ]);

        foreach ($validated['products'] as $product) {
            $purchase->items()->create([
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'purchase_price' => $product['purchase_price'],
                'total_price' => $product['quantity'] * $product['purchase_price'],
            ]);
        }

        // Add stock from purchase
        StockService::addStockFromPurchase($purchase->items);

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        return view('admin.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $products = Product::all();
        $purchase->load('items.product');
        return view('admin.purchases.edit', compact('purchase', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'supplier_name' => 'required|string|max:255',
            'bill_details' => 'nullable|string',
            'transportation_cost' => 'nullable|numeric|min:0',
            'bill_due_date' => 'nullable|date',
            'expense' => 'nullable|numeric|min:0',
            'expense_details' => 'nullable|string',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'purchase_price' => 'required|array|min:1',
            'purchase_price.*' => 'required|numeric|min:0',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['product_id'] as $key => $productId) {
            $totalAmount += $validated['quantity'][$key] * $validated['purchase_price'][$key];
        }
        $totalAmount += $validated['transportation_cost'] ?? 0;

        // Update purchase
        $purchase->update([
            'purchase_date' => $validated['purchase_date'],
            'supplier_name' => $validated['supplier_name'],
            'bill_details' => $validated['bill_details'],
            'transportation_cost' => $validated['transportation_cost'] ?? 0,
            'bill_due_date' => $validated['bill_due_date'],
            'expense' => $validated['expense'] ?? 0,
            'expense_details' => $validated['expense_details'],
            'total_amount' => $totalAmount,
        ]);

        // Remove old stock before deleting items
        StockService::removeStockFromPurchase($purchase->items);

        // Delete existing items and create new ones
        $purchase->items()->delete();
        foreach ($validated['product_id'] as $key => $productId) {
            $purchase->items()->create([
                'product_id' => $productId,
                'quantity' => $validated['quantity'][$key],
                'purchase_price' => $validated['purchase_price'][$key],
                'total_price' => $validated['quantity'][$key] * $validated['purchase_price'][$key],
            ]);
        }

        // Add new stock
        StockService::addStockFromPurchase($purchase->items);

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        // Remove stock before deleting
        StockService::removeStockFromPurchase($purchase->items);

        $purchase->items()->delete();
        $purchase->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted successfully.');
    }

    public function getPaymentDetails(Purchase $purchase)
    {
        $purchase->load('items.product', 'payments');

        $items = $purchase->items->map(function ($item) {
            return [
                'product_name' => $item->product->product_name,
                'quantity' => $item->quantity,
                'purchase_price' => number_format($item->purchase_price, 2),
                'total_price' => number_format($item->total_price, 2),
            ];
        });

        $payments = $purchase->payments->map(function ($payment) {
            return [
                'payment_date' => $payment->payment_date->format('d M Y'),
                'amount' => number_format($payment->amount, 2),
                'payment_method' => $payment->getPaymentMethodLabel(),
                'payment_status' => ucfirst($payment->payment_status),
                'reference_number' => $payment->reference_number ?? '-',
                'notes' => $payment->notes ?? '-',
            ];
        });

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
     * Add payment for a purchase
     */
    public function addPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,credit_card,other',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment = $purchase->payments()->create([
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'],
            'notes' => $validated['notes'],
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
            'total_paid' => number_format($purchase->getTotalPaidAmount() + $validated['amount'], 2),
            'remaining_amount' => number_format($purchase->getRemainingAmount() - $validated['amount'], 2),
        ]);
    }
}
