<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use App\Models\Purchase;
use App\Models\Product;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $totalSales = Sell::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $totalProfit = $totalSales - $totalPurchases;
        $pendingPayments = Sell::where('payment_status', '!=', 'paid')->sum('pending_amount');
        $totalProducts = Product::count();

        return view('admin.reports.index', compact(
            'totalSales',
            'totalPurchases',
            'totalProfit',
            'pendingPayments',
            'totalProducts'
        ));
    }

    /**
     * Display sales report
     */
    public function sales()
    {
        $sells = Sell::with('items.product')->get();
        $totalSales = $sells->sum('total_amount');
        $totalQuantity = $sells->flatMap->items->sum('quantity');
        $paidAmount = $sells->sum('amount_paid');
        $pendingAmount = $sells->sum('pending_amount');

        return view('admin.reports.sales', compact(
            'sells',
            'totalSales',
            'totalQuantity',
            'paidAmount',
            'pendingAmount'
        ));
    }

    /**
     * Display purchases report
     */
    public function purchases()
    {
        $purchases = Purchase::with('items')->get();
        $totalPurchases = $purchases->sum('total_amount');
        $totalItems = $purchases->flatMap->items->count();

        return view('admin.reports.purchases', compact(
            'purchases',
            'totalPurchases',
            'totalItems'
        ));
    }

    /**
     * Display stock report
     */
    public function stock()
    {
        $products = Product::with('purchaseItems', 'sellItems', 'purchaseReturns', 'sellReturns')->get();

        // Calculate stock data for each product
        $stockData = $products->map(function ($product) {
            $totalPurchase = $product->purchaseItems->sum('quantity');
            $totalSell = $product->sellItems->sum('quantity');
            $purchaseReturn = $product->purchaseReturns->sum('quantity');
            $sellReturn = $product->sellReturns->sum('quantity');
            $availableStock = $totalPurchase - $purchaseReturn - $totalSell + $sellReturn;

            return [
                'product' => $product,
                'total_purchase' => $totalPurchase,
                'total_sell' => $totalSell,
                'purchase_return' => $purchaseReturn,
                'sell_return' => $sellReturn,
                'available_stock' => max(0, $availableStock),
            ];
        });

        $totalProducts = $products->count();

        return view('admin.reports.stock', compact(
            'stockData',
            'totalProducts'
        ));
    }
}
