<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with('items')->get();
        return view('admin.purchases.index', compact('purchases'));
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

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->items()->delete();
        $purchase->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted successfully.');
    }
}
