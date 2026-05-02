<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\SellItem;
use App\Models\SellReturn;
use App\Models\StockClosing;
use App\Models\StockClosingItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockClosingController extends Controller
{
    /**
     * Runtime verification tool: pick start/end dates,
     * enter opening + closing qty, see expected vs actual.
     * Nothing saved.
     */
    public function index(Request $request): View
    {
        $today = \Carbon\Carbon::today();
        $defaultStart = $today->copy()->startOfMonth()->format('Y-m-d');
        $defaultEnd   = $today->format('Y-m-d');

        $startDate = $request->input('start_date', $defaultStart);
        $endDate   = $request->input('end_date', $defaultEnd);

        try {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
            $end   = \Carbon\Carbon::parse($endDate)->endOfDay();
        } catch (\Throwable) {
            $start = \Carbon\Carbon::parse($defaultStart)->startOfDay();
            $end   = \Carbon\Carbon::parse($defaultEnd)->endOfDay();
            $startDate = $defaultStart;
            $endDate   = $defaultEnd;
        }

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $startStr = $start->toDateString();
        $endStr   = $end->toDateString();

        $products = Product::orderByName()->get();

        $rows = $products->map(function (Product $product) use ($startStr, $endStr) {
            $purchased = (float) PurchaseItem::whereHas('purchase', function ($q) use ($startStr, $endStr) {
                $q->whereBetween('purchase_date', [$startStr, $endStr]);
            })->where('product_id', $product->id)->sum('quantity');

            $purchaseReturns = (float) PurchaseReturn::where('product_id', $product->id)
                ->whereBetween('return_date', [$startStr, $endStr])
                ->sum('quantity');

            $sold = (float) SellItem::whereHas('sell', function ($q) use ($startStr, $endStr) {
                $q->whereBetween('sell_date', [$startStr, $endStr]);
            })->where('product_id', $product->id)->sum('quantity');

            $sellReturns = (float) SellReturn::where('product_id', $product->id)
                ->whereBetween('return_date', [$startStr, $endStr])
                ->sum('quantity');

            return [
                'product_id'           => $product->id,
                'product_name'         => $product->product_name,
                'purchased_qty'        => $purchased,
                'purchase_returns_qty' => $purchaseReturns,
                'sold_qty'             => $sold,
                'sell_returns_qty'     => $sellReturns,
            ];
        });

        $modalProducts       = Product::active()->orderByName()->get();
        $suggestedClosingQty = $this->suggestedClosingQty();

        return view('admin.stock-closings.index', compact(
            'rows', 'startDate', 'endDate', 'modalProducts', 'suggestedClosingQty'
        ));
    }

    /**
     * Most recent saved closing's actual_stock keyed by product_id.
     * Used to pre-fill the Save-Closing modal.
     */
    private function suggestedClosingQty(): \Illuminate\Support\Collection
    {
        $latest = StockClosing::orderBy('closing_date', 'desc')->first();
        if (!$latest) {
            return collect();
        }
        return $latest->items->mapWithKeys(fn ($i) => [$i->product_id => (float) $i->actual_stock]);
    }

    /**
     * Save the closing record.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'closing_date'      => ['required', 'date'],
            'product_id'        => ['required', 'array', 'min:1'],
            'product_id.*'      => ['required', 'exists:products,id'],
            'actual_stock'      => ['required', 'array'],
            'actual_stock.*'    => ['required', 'numeric', 'min:0'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ]);

        $closingDate = $request->closing_date;
        $periodLabel = \Carbon\Carbon::parse($closingDate)->format('Y-m');

        DB::transaction(function () use ($request, $closingDate, $periodLabel) {
            $closing = StockClosing::create([
                'closing_date' => $closingDate,
                'period_label' => $periodLabel,
                'notes'        => $request->notes,
            ]);

            $submittedIds = [];

            // Active products: save with the qty entered in the modal
            foreach ($request->product_id as $i => $productId) {
                $submittedIds[] = (int) $productId;
                StockClosingItem::create([
                    'stock_closing_id'     => $closing->id,
                    'product_id'           => $productId,
                    'opening_stock'        => 0,
                    'purchased_qty'        => 0,
                    'purchase_returns_qty' => 0,
                    'sold_qty'             => 0,
                    'sell_returns_qty'     => 0,
                    'expected_stock'       => 0,
                    'actual_stock'         => (float) ($request->actual_stock[$i] ?? 0),
                    'difference'           => 0,
                ]);
            }

            // Remaining (inactive) products: auto-save with qty = 0
            $remainingIds = Product::whereNotIn('id', $submittedIds)->pluck('id');
            foreach ($remainingIds as $productId) {
                StockClosingItem::create([
                    'stock_closing_id'     => $closing->id,
                    'product_id'           => $productId,
                    'opening_stock'        => 0,
                    'purchased_qty'        => 0,
                    'purchase_returns_qty' => 0,
                    'sold_qty'             => 0,
                    'sell_returns_qty'     => 0,
                    'expected_stock'       => 0,
                    'actual_stock'         => 0,
                    'difference'           => 0,
                ]);
            }
        });

        return redirect()->route('admin.stock-closings.history')
            ->with('success', 'Closing saved successfully.');
    }

    /**
     * List all saved closings (history).
     */
    public function history(): View
    {
        $closings = StockClosing::withCount('items')
            ->orderBy('closing_date', 'desc')
            ->paginate(20);

        $modalProducts       = Product::active()->orderByName()->get();
        $suggestedClosingQty = $this->suggestedClosingQty();

        return view('admin.stock-closings.history', compact(
            'closings', 'modalProducts', 'suggestedClosingQty'
        ));
    }

    /**
     * Show details of a saved closing.
     */
    public function show(StockClosing $stockClosing): View
    {
        $stockClosing->load('items.product');

        return view('admin.stock-closings.show', compact('stockClosing'));
    }

    public function destroy(StockClosing $stockClosing): RedirectResponse
    {
        $stockClosing->items()->delete();
        $stockClosing->delete();

        return redirect()->route('admin.stock-closings.history')
            ->with('success', 'Closing deleted.');
    }
}
