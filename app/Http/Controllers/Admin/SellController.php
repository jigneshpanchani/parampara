<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use App\Models\SellItem;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class SellController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sells = Sell::with('items.product')->latest()->paginate(20);
        return view('admin.sells.index', compact('sells'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('admin.sells.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sell_date' => 'required|date',
            'seller_name' => 'nullable|string|max:255',
            'seller_contact_number' => 'nullable|string|max:20',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'selling_price' => 'required|array|min:1',
            'selling_price.*' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,upi,qr,mix',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['product_id'] as $key => $productId) {
            $totalAmount += $validated['quantity'][$key] * $validated['selling_price'][$key];
        }

        $pendingAmount = max($totalAmount - $validated['amount_paid'], 0);
        $paymentStatus = 'pending';
        if ($validated['amount_paid'] >= $totalAmount && $totalAmount > 0) {
            $paymentStatus = 'paid';
            $pendingAmount = 0;
        } elseif ($validated['amount_paid'] > 0) {
            $paymentStatus = 'partial';
        }

        // Create sell record
        $sell = Sell::create([
            'sell_date' => $validated['sell_date'],
            'total_amount' => $totalAmount,
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => $paymentStatus,
            'amount_paid' => $validated['amount_paid'],
            'pending_amount' => $pendingAmount,
            'notes' => $validated['notes'],
        ]);

        // Create sell items
        foreach ($validated['product_id'] as $key => $productId) {
            $itemTotal = $validated['quantity'][$key] * $validated['selling_price'][$key];
            SellItem::create([
                'sell_id' => $sell->id,
                'product_id' => $productId,
                'quantity' => $validated['quantity'][$key],
                'selling_price' => $validated['selling_price'][$key],
                'total_price' => $itemTotal,
            ]);
        }

        // Deduct stock from sale
        StockService::deductStockFromSale($sell->items);

        return redirect()->route('admin.sells.index')->with('success', 'Sale recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sell $sell)
    {
        return view('admin.sells.show', compact('sell'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sell $sell)
    {
        $products = Product::all();
        $sell->load('items.product');
        return view('admin.sells.edit', compact('sell', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sell $sell)
    {
        $validated = $request->validate([
            'sell_date' => 'required|date',
            'seller_name' => 'nullable|string|max:255',
            'seller_contact_number' => 'nullable|string|max:20',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1',
            'selling_price' => 'required|array|min:1',
            'selling_price.*' => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,upi,qr,mix',
            'amount_paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Calculate total amount
        $totalAmount = 0;
        foreach ($validated['product_id'] as $key => $productId) {
            $totalAmount += $validated['quantity'][$key] * $validated['selling_price'][$key];
        }

        $pendingAmount = max($totalAmount - $validated['amount_paid'], 0);
        $paymentStatus = 'pending';
        if ($validated['amount_paid'] >= $totalAmount && $totalAmount > 0) {
            $paymentStatus = 'paid';
            $pendingAmount = 0;
        } elseif ($validated['amount_paid'] > 0) {
            $paymentStatus = 'partial';
        }

        // Update sell record
        $sell->update([
            'sell_date' => $validated['sell_date'],
            'total_amount' => $totalAmount,
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => $paymentStatus,
            'amount_paid' => $validated['amount_paid'],
            'pending_amount' => $pendingAmount,
            'notes' => $validated['notes'],
        ]);

        // Add back old stock before deleting items
        StockService::addStockBackFromSale($sell->items);

        // Delete old items and create new ones
        $sell->items()->delete();
        foreach ($validated['product_id'] as $key => $productId) {
            $itemTotal = $validated['quantity'][$key] * $validated['selling_price'][$key];
            SellItem::create([
                'sell_id' => $sell->id,
                'product_id' => $productId,
                'quantity' => $validated['quantity'][$key],
                'selling_price' => $validated['selling_price'][$key],
                'total_price' => $itemTotal,
            ]);
        }

        // Deduct new stock
        StockService::deductStockFromSale($sell->items);

        return redirect()->route('admin.sells.index')->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sell $sell)
    {
        // Add back stock before deleting
        StockService::addStockBackFromSale($sell->items);

        $sell->items()->delete();
        $sell->delete();
        return redirect()->route('admin.sells.index')->with('success', 'Sale deleted successfully.');
    }
}
