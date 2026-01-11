<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReturn;
use App\Models\Purchase;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:List Purchase Return')->only(['index']);
        //$this->middleware('permission:Create Purchase Return')->only(['create', 'store']);
        //$this->middleware('permission:Edit Purchase Return')->only(['edit', 'update']);
        //$this->middleware('permission:Delete Purchase Return')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = PurchaseReturn::with(['purchase', 'product'])->latest()->paginate(20);
        return view('admin.purchase-returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $purchases = Purchase::latest()->get();
        $products = Product::all();
        return view('admin.purchase-returns.create', compact('purchases', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'return_price' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_return_amount'] = $validated['quantity'] * $validated['return_price'];

        $return = PurchaseReturn::create($validated);

        // Deduct stock from purchase return
        StockService::deductStockFromPurchaseReturn($return);

        return redirect()->route('admin.purchase-returns.index')->with('success', 'Purchase return created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseReturn $purchaseReturn)
    {
        return view('admin.purchase-returns.show', compact('purchaseReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseReturn $purchaseReturn)
    {
        $purchases = Purchase::latest()->get();
        $products = Product::all();
        return view('admin.purchase-returns.edit', compact('purchaseReturn', 'purchases', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'return_price' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_return_amount'] = $validated['quantity'] * $validated['return_price'];

        // Add back old stock before updating
        StockService::addStockBackFromPurchaseReturn($purchaseReturn);

        $purchaseReturn->update($validated);

        // Deduct new stock
        StockService::deductStockFromPurchaseReturn($purchaseReturn);

        return redirect()->route('admin.purchase-returns.index')->with('success', 'Purchase return updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseReturn $purchaseReturn)
    {
        // Add back stock before deleting
        StockService::addStockBackFromPurchaseReturn($purchaseReturn);

        $purchaseReturn->delete();
        return redirect()->route('admin.purchase-returns.index')->with('success', 'Purchase return deleted successfully.');
    }
}
