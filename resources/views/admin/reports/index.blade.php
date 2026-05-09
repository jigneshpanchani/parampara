@extends('layouts.admin')

@section('title', 'Reports Dashboard')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📊 Reports Dashboard</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <!-- Total Purchases Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Purchases</p>
                <p class="text-3xl font-bold text-gray-800">₹{{ number_format($totalPurchases, 2) }}</p>
            </div>
            <div class="text-4xl text-orange-500">🛒</div>
        </div>
    </div>

    <!-- Total Sales Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Sales</p>
                <p class="text-3xl font-bold text-gray-800">₹{{ number_format($totalSales, 2) }}</p>
            </div>
            <div class="text-4xl text-blue-500">💰</div>
        </div>
    </div>

    <!-- Total Profit Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Profit</p>
                <p class="text-3xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($totalProfit, 2) }}</p>
            </div>
            <div class="text-4xl">📈</div>
        </div>
    </div>

    <!-- Pending Payments Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Pending Payments</p>
                <p class="text-3xl font-bold text-red-600">₹{{ number_format($pendingPayments, 2) }}</p>
            </div>
            <div class="text-4xl">⏳</div>
        </div>
    </div>

    <!-- Total Products Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Products</p>
                <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    <span class="text-green-600 font-semibold">Active {{ $activeProducts }}</span>
                    &amp;
                    <span class="text-red-600 font-semibold">InActive {{ $inactiveProducts }}</span>
                </p>
            </div>
            <div class="text-4xl">📦</div>
        </div>
    </div>
</div>

<!-- Report Links -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Sales Report -->
    <a href="{{ route('admin.reports.sales') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="text-4xl mb-4">💳</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Sales Report</h3>
        <p class="text-gray-600 text-sm">View detailed sales transactions and payment status</p>
    </a>

    <!-- Purchases Report -->
    <a href="{{ route('admin.reports.purchases') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="text-4xl mb-4">📋</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Purchases Report</h3>
        <p class="text-gray-600 text-sm">Track all purchase orders and supplier details</p>
    </a>

    <!-- Stock Report -->
    <a href="{{ route('admin.reports.stock') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
        <div class="text-4xl mb-4">📦</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Stock Report</h3>
        <p class="text-gray-600 text-sm">Monitor product inventory and pricing</p>
    </a>

    <!-- Profit Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-4xl mb-4">📊</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Profit Analysis</h3>
        <p class="text-gray-600 text-sm">Profit margin: <strong>{{ $totalSales > 0 ? number_format(($totalProfit / $totalSales) * 100, 2) : 0 }}%</strong></p>
    </div>
</div>
@endsection

