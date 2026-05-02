<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaleReturn;
use App\Models\Sale;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:List Sale Return')->only(['index']);
        //$this->middleware('permission:Create Sale Return')->only(['create', 'store']);
        //$this->middleware('permission:Edit Sale Return')->only(['edit', 'update']);
        //$this->middleware('permission:Delete Sale Return')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = SaleReturn::with(['sale', 'product'])->latest()->paginate(20);
        return view('admin.sale-returns.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sales = Sale::latest()->get();
        $products = Product::all();
        return view('admin.sale-returns.create', compact('sales', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'return_price' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_return_amount'] = $validated['quantity'] * $validated['return_price'];

        DB::transaction(function () use ($validated, &$return) {
            $return = SaleReturn::create($validated);
            StockService::addStockFromSaleReturn($return);
        });

        return redirect()->route('admin.sale-returns.index')->with('success', 'Sale return created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SaleReturn $saleReturn)
    {
        return view('admin.sale-returns.show', compact('saleReturn'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleReturn $saleReturn)
    {
        $sales = Sale::latest()->get();
        $products = Product::all();
        return view('admin.sale-returns.edit', compact('saleReturn', 'sales', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleReturn $saleReturn)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'return_price' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['total_return_amount'] = $validated['quantity'] * $validated['return_price'];

        DB::transaction(function () use ($validated, $saleReturn) {
            StockService::removeStockFromSaleReturn($saleReturn);
            $saleReturn->update($validated);
            StockService::addStockFromSaleReturn($saleReturn);
        });

        return redirect()->route('admin.sale-returns.index')->with('success', 'Sale return updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SaleReturn $saleReturn)
    {
        DB::transaction(function () use ($saleReturn) {
            StockService::removeStockFromSaleReturn($saleReturn);
            $saleReturn->delete();
        });

        return redirect()->route('admin.sale-returns.index')->with('success', 'Sale return deleted successfully.');
    }
}
