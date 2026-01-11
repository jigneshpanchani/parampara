<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'base_price_min' => 'required|numeric|min:0',
            'base_price_max' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $path = $file->store('products', 'public');
                $validated['photo'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload photo: ' . $e->getMessage());
            }
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|unique:products,product_code,' . $product->id,
            'description' => 'nullable|string',
            'base_price_min' => 'required|numeric|min:0',
            'base_price_max' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            try {
                $file = $request->file('photo');
                $path = $file->store('products', 'public');
                $validated['photo'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Failed to upload photo: ' . $e->getMessage());
            }
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}
