<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSellRequest;
use App\Http\Requests\Admin\UpdateSellRequest;
use App\Models\Product;
use App\Models\Sell;
use App\Services\SellService;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellController extends Controller
{
    public function __construct(
        private SellService $sellService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Sell::with(['items.product', 'cashSellInvoice', 'onlineSellInvoice', 'mixSellInvoice'])
            ->orderBy('sell_date', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('sell_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sell_date', '<=', $request->date_to);
        }
        if ($request->filled('payment_mode') && in_array($request->payment_mode, ['cash', 'upi', 'gpay', 'mix'], true)) {
            $query->where('payment_mode', $request->payment_mode);
        }
        if ($request->filled('payment_status') && in_array($request->payment_status, ['paid', 'pending', 'partial'], true)) {
            $query->where('payment_status', $request->payment_status);
        }

        $sells = $query->paginate(20)->withQueryString();

        return view('admin.sells.index', compact('sells'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $products = Product::orderByName()->get();

        return view('admin.sells.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSellRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $totalAmount = $this->sellService->calculateTotalAmount(
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $attributes = $this->sellService->buildSellAttributes($validated, $totalAmount);
            $sell = Sell::create($attributes);

            $this->sellService->createSellItems(
                $sell,
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            StockService::deductStockFromSale($sell->items);
        });

        return redirect()->route('admin.sells.index')
            ->with('success', 'Sale recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sell $sell): View
    {
        $sell->load(['items.product', 'cashSellInvoice', 'onlineSellInvoice', 'mixSellInvoice']);
        return view('admin.sells.show', compact('sell'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sell $sell): View
    {
        $products = Product::orderByName()->get();
        $sell->load('items.product');

        return view('admin.sells.edit', compact('sell', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSellRequest $request, Sell $sell): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $sell) {
            $totalAmount = $this->sellService->calculateTotalAmount(
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $attributes = $this->sellService->buildSellAttributes($validated, $totalAmount);
            $sell->update($attributes);

            StockService::addStockBackFromSale($sell->items);

            $sell->items()->delete();
            $this->sellService->createSellItems(
                $sell,
                $validated['product_id'],
                $validated['quantity'],
                $validated['selling_price']
            );

            $sell->load('items');
            StockService::deductStockFromSale($sell->items);
        });

        return redirect()->route('admin.sells.index')
            ->with('success', 'Sale updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sell $sell): RedirectResponse
    {
        DB::transaction(function () use ($sell) {
            StockService::addStockBackFromSale($sell->items);

            $sell->returns()->delete();
            $sell->items()->delete();
            $sell->delete();
        });

        return redirect()->route('admin.sells.index')
            ->with('success', 'Sale deleted successfully.');
    }
}
