<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Expense;
use App\Models\PurchaseReturn;
use App\Models\SaleReturn;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display main dashboard
     */
    public function index()
    {
        // Sales Metrics
        $totalSales = Sale::sum('total_amount');
        $totalSalesCount = Sale::count();
        $monthlySales = Sale::whereYear('sale_date', now()->year)->whereMonth('sale_date', now()->month)->sum('total_amount');

        // Purchase Metrics
        $totalPurchases = Purchase::sum('total_amount');
        $totalPurchasesCount = Purchase::count();
        $monthlyPurchases = Purchase::whereYear('purchase_date', now()->year)->whereMonth('purchase_date', now()->month)->sum('total_amount');

        // Expense Metrics
        $totalExpenses = Expense::sum('amount');
        $monthlyExpenses = Expense::whereYear('expense_date', now()->year)->whereMonth('expense_date', now()->month)->sum('amount');

        // Return Metrics
        $totalPurchaseReturns = PurchaseReturn::sum('total_return_amount');
        $totalSaleReturns = SaleReturn::sum('total_return_amount');

        // Profit Calculation
        $totalProfit = $totalSales - $totalPurchases - $totalExpenses;
        $monthlyProfit = $monthlySales - $monthlyPurchases - $monthlyExpenses;

        // Payment Metrics
        $pendingPayments = Sale::where('payment_status', '!=', 'paid')->sum('pending_amount');

        // Product Metrics
        $totalProducts = Product::count();
        $lowStockProducts = Stock::where('quantity', '<', 10)->count();

        // Recent Data
        $recentSales = Sale::latest()->take(5)->get();
        $recentPurchases = Purchase::latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();

        return view('admin.dashboard.index', compact(
            'totalSales',
            'totalSalesCount',
            'monthlySales',
            'totalPurchases',
            'totalPurchasesCount',
            'monthlyPurchases',
            'totalExpenses',
            'monthlyExpenses',
            'totalPurchaseReturns',
            'totalSaleReturns',
            'totalProfit',
            'monthlyProfit',
            'pendingPayments',
            'totalProducts',
            'lowStockProducts',
            'recentSales',
            'recentPurchases',
            'recentExpenses'
        ));
    }

    /**
     * Display financial overview
     */
    public function financial()
    {
        $totalSales = Sale::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $totalProfit = $totalSales - $totalPurchases - $totalExpenses;

        $monthlySales = Sale::whereYear('sale_date', now()->year)->whereMonth('sale_date', now()->month)->sum('total_amount');
        $monthlyPurchases = Purchase::whereYear('purchase_date', now()->year)->whereMonth('purchase_date', now()->month)->sum('total_amount');
        $monthlyExpenses = Expense::whereYear('expense_date', now()->year)->whereMonth('expense_date', now()->month)->sum('amount');
        $monthlyProfit = $monthlySales - $monthlyPurchases - $monthlyExpenses;

        $profitMargin = $totalSales > 0 ? ($totalProfit / $totalSales) * 100 : 0;

        return view('admin.dashboard.financial', compact(
            'totalSales',
            'totalPurchases',
            'totalExpenses',
            'totalProfit',
            'monthlySales',
            'monthlyPurchases',
            'monthlyExpenses',
            'monthlyProfit',
            'profitMargin'
        ));
    }

    /**
     * Display inventory overview
     */
    public function inventory()
    {
        $totalProducts = Product::count();
        $lowStockProducts = Stock::where('quantity', '<', 10)->get();
        $outOfStockProducts = Stock::where('quantity', '=', 0)->count();
        $totalInventoryValue = Stock::sum(\DB::raw('quantity * unit_price'));

        return view('admin.dashboard.inventory', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'totalInventoryValue'
        ));
    }
}

