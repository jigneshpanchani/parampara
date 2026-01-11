<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellReturn;
use App\Models\Sell;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class SellReturnController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:List Sell Return')->only(['index']);
        //$this->middleware('permission:Create Sell Return')->only(['create', 'store']);
        //$this->middleware('permission:Edit Sell Return')->only(['edit', 'update']);
        //$this->middleware('permission:Delete Sell Return')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = SellReturn::with(['sell', 'product'])->latest()->paginate(20);
        return view('admin.sell-returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sells = Sell::latest()->get();
        $products = Product::all();
        return view('admin.sell-returns.create', compact('sells', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sell_id' => 'required|exists:sells,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'return_price' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_return_amount'] = $validated['quantity'] * $validated['return_price'];

        $return = SellReturn::create($validated);

        // Add stock from sell return
        StockService::addStockFromSellReturn($return);

        return redirect()->route('admin.sell-returns.index')->with('success', 'Sell return created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SellReturn $sellReturn)
    {
        return view('admin.sell-returns.show', compact('sellReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SellReturn $sellReturn)
    {
        $sells = Sell::latest()->get();
        $products = Product::all();
        return view('admin.sell-returns.edit', compact('sellReturn', 'sells', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SellReturn $sellReturn)
    {
        $validated = $request->validate([
            'sell_id' => 'required|exists:sells,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'return_price' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_return_amount'] = $validated['quantity'] * $validated['return_price'];

        // Remove old stock before updating
        StockService::removeStockFromSellReturn($sellReturn);

        $sellReturn->update($validated);

        // Add new stock
        StockService::addStockFromSellReturn($sellReturn);

        return redirect()->route('admin.sell-returns.index')->with('success', 'Sell return updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SellReturn $sellReturn)
    {
        // Remove stock before deleting
        StockService::removeStockFromSellReturn($sellReturn);

        $sellReturn->delete();
        return redirect()->route('admin.sell-returns.index')->with('success', 'Sell return deleted successfully.');
    }
}
