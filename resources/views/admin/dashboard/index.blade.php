@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📊 Dashboard</h2>
    <p class="text-gray-600 mt-2">Welcome back! Here's your business overview.</p>
</div>

<!-- Key Metrics Row 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales Card -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Sales</p>
                <p class="text-3xl font-bold text-blue-600">₹{{ number_format($totalSales, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $totalSalesCount }} transactions</p>
            </div>
            <div class="text-5xl opacity-20">💰</div>
        </div>
    </div>

    <!-- Total Purchases Card -->
    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Purchases</p>
                <p class="text-3xl font-bold text-orange-600">₹{{ number_format($totalPurchases, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $totalPurchasesCount }} orders</p>
            </div>
            <div class="text-5xl opacity-20">🛒</div>
        </div>
    </div>

    <!-- Total Expenses Card -->
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow p-6 border-l-4 border-red-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Expenses</p>
                <p class="text-3xl font-bold text-red-600">₹{{ number_format($totalExpenses, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Monthly: ₹{{ number_format($monthlyExpenses, 2) }}</p>
            </div>
            <div class="text-5xl opacity-20">💸</div>
        </div>
    </div>

    <!-- Total Profit Card -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Profit</p>
                <p class="text-3xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($totalProfit, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Monthly: ₹{{ number_format($monthlyProfit, 2) }}</p>
            </div>
            <div class="text-5xl opacity-20">📈</div>
        </div>
    </div>
</div>

<!-- Key Metrics Row 2 -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Pending Payments Card -->
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Pending Payments</p>
                <p class="text-3xl font-bold text-yellow-600">₹{{ number_format($pendingPayments, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Awaiting payment</p>
            </div>
            <div class="text-5xl opacity-20">⏳</div>
        </div>
    </div>

    <!-- Total Products Card -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Products</p>
                <p class="text-3xl font-bold text-purple-600">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $lowStockProducts }} low stock</p>
            </div>
            <div class="text-5xl opacity-20">📦</div>
        </div>
    </div>

    <!-- Purchase Returns Card -->
    <div class="bg-gradient-to-br from-pink-50 to-pink-100 rounded-lg shadow p-6 border-l-4 border-pink-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Purchase Returns</p>
                <p class="text-3xl font-bold text-pink-600">₹{{ number_format($totalPurchaseReturns, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Supplier returns</p>
            </div>
            <div class="text-5xl opacity-20">↩️</div>
        </div>
    </div>

    <!-- Sell Returns Card -->
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg shadow p-6 border-l-4 border-indigo-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Sell Returns</p>
                <p class="text-3xl font-bold text-indigo-600">₹{{ number_format($totalSellReturns, 2) }}</p>
                <p class="text-xs text-gray-500 mt-1">Customer returns</p>
            </div>
            <div class="text-5xl opacity-20">↩️</div>
        </div>
    </div>
</div>

<!-- Dashboard Links -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <!-- Financial Overview -->
    <a href="{{ route('admin.dashboard.financial') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-t-4 border-blue-500">
        <div class="text-4xl mb-4">💹</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Financial Overview</h3>
        <p class="text-gray-600 text-sm">Detailed financial analysis and profit margins</p>
    </a>

    <!-- Inventory Overview -->
    <a href="{{ route('admin.dashboard.inventory') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-t-4 border-purple-500">
        <div class="text-4xl mb-4">📊</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Inventory Overview</h3>
        <p class="text-gray-600 text-sm">Stock levels and inventory management</p>
    </a>

    <!-- Reports -->
    <a href="{{ route('admin.reports.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-t-4 border-green-500">
        <div class="text-4xl mb-4">📈</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Detailed Reports</h3>
        <p class="text-gray-600 text-sm">Sales, purchases, and stock reports</p>
    </a>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Sales -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Sales</h3>
        <div class="space-y-3">
            @forelse($recentSales as $sale)
                <div class="flex justify-between items-center pb-3 border-b">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Sale #{{ $sale->id }}</p>
                        <p class="text-xs text-gray-500">{{ $sale->sell_date->format('d M Y') }}</p>
                    </div>
                    <p class="text-sm font-bold text-blue-600">₹{{ number_format($sale->total_amount, 2) }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No recent sales</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Purchases</h3>
        <div class="space-y-3">
            @forelse($recentPurchases as $purchase)
                <div class="flex justify-between items-center pb-3 border-b">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">PO #{{ $purchase->id }}</p>
                        <p class="text-xs text-gray-500">{{ $purchase->purchase_date->format('d M Y') }}</p>
                    </div>
                    <p class="text-sm font-bold text-orange-600">₹{{ number_format($purchase->total_amount, 2) }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No recent purchases</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Expenses</h3>
        <div class="space-y-3">
            @forelse($recentExpenses as $expense)
                <div class="flex justify-between items-center pb-3 border-b">
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ $expense->category }}</p>
                        <p class="text-xs text-gray-500">{{ $expense->expense_date->format('d M Y') }}</p>
                    </div>
                    <p class="text-sm font-bold text-red-600">₹{{ number_format($expense->amount, 2) }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm">No recent expenses</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

