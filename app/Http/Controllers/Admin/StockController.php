<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource - Show all products with their stock
     */
    public function index()
    {
        $products = Product::all();
        return view('admin.stocks.index', compact('products'));
    }

    /**
     * Store forcefully added stock
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $product->stock_quantity = $validated['quantity'];
        $product->save();

        return redirect()->route('admin.stocks.index')->with('success', 'Stock updated successfully.');
    }
}
