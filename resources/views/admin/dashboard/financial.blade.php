@extends('layouts.admin')

@section('title', 'Financial Overview')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">💹 Financial Overview</h2>
    <p class="text-gray-600 mt-2">Comprehensive financial analysis and metrics</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales -->
    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow p-6 border-l-4 border-blue-500">
        <p class="text-gray-600 text-sm font-semibold">Total Sales</p>
        <p class="text-3xl font-bold text-blue-600 mt-2">₹{{ number_format($totalSales, 2) }}</p>
        <p class="text-xs text-gray-500 mt-2">All time sales</p>
    </div>

    <!-- Total Purchases -->
    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg shadow p-6 border-l-4 border-orange-500">
        <p class="text-gray-600 text-sm font-semibold">Total Purchases</p>
        <p class="text-3xl font-bold text-orange-600 mt-2">₹{{ number_format($totalPurchases, 2) }}</p>
        <p class="text-xs text-gray-500 mt-2">All time purchases</p>
    </div>

    <!-- Total Expenses -->
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow p-6 border-l-4 border-red-500">
        <p class="text-gray-600 text-sm font-semibold">Total Expenses</p>
        <p class="text-3xl font-bold text-red-600 mt-2">₹{{ number_format($totalExpenses, 2) }}</p>
        <p class="text-xs text-gray-500 mt-2">All time expenses</p>
    </div>

    <!-- Total Profit -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-gray-600 text-sm font-semibold">Total Profit</p>
        <p class="text-3xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">₹{{ number_format($totalProfit, 2) }}</p>
        <p class="text-xs text-gray-500 mt-2">Net profit</p>
    </div>
</div>

<!-- Monthly Metrics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Monthly Breakdown -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">This Month's Breakdown</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center pb-4 border-b">
                <div>
                    <p class="text-gray-700 font-semibold">Monthly Sales</p>
                    <p class="text-sm text-gray-500">Current month</p>
                </div>
                <p class="text-2xl font-bold text-blue-600">₹{{ number_format($monthlySales, 2) }}</p>
            </div>
            <div class="flex justify-between items-center pb-4 border-b">
                <div>
                    <p class="text-gray-700 font-semibold">Monthly Purchases</p>
                    <p class="text-sm text-gray-500">Current month</p>
                </div>
                <p class="text-2xl font-bold text-orange-600">₹{{ number_format($monthlyPurchases, 2) }}</p>
            </div>
            <div class="flex justify-between items-center pb-4 border-b">
                <div>
                    <p class="text-gray-700 font-semibold">Monthly Expenses</p>
                    <p class="text-sm text-gray-500">Current month</p>
                </div>
                <p class="text-2xl font-bold text-red-600">₹{{ number_format($monthlyExpenses, 2) }}</p>
            </div>
            <div class="flex justify-between items-center pt-4 bg-gray-50 p-4 rounded">
                <div>
                    <p class="text-gray-700 font-bold">Monthly Profit</p>
                    <p class="text-sm text-gray-500">Current month</p>
                </div>
                <p class="text-2xl font-bold {{ $monthlyProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($monthlyProfit, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Profit Analysis -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Profit Analysis</h3>
        <div class="space-y-6">
            <!-- Profit Margin -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <p class="text-gray-700 font-semibold">Profit Margin</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($profitMargin, 2) }}%</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($profitMargin, 100) }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Profit as percentage of sales</p>
            </div>

            <!-- Cost Breakdown -->
            <div>
                <p class="text-gray-700 font-semibold mb-4">Cost Breakdown</p>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Purchase Cost</span>
                        <span class="font-semibold">{{ number_format(($totalPurchases / $totalSales) * 100, 2) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Operating Expenses</span>
                        <span class="font-semibold">{{ number_format(($totalExpenses / $totalSales) * 100, 2) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Net Profit</span>
                        <span class="font-semibold text-green-600">{{ number_format(($totalProfit / $totalSales) * 100, 2) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary Table -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-xl font-bold text-gray-800 mb-6">Financial Summary</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Metric</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">All Time</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">This Month</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Percentage</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">Total Sales</td>
                    <td class="px-6 py-4 text-right text-sm text-blue-600 font-bold">₹{{ number_format($totalSales, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm text-blue-600 font-bold">₹{{ number_format($monthlySales, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm">{{ $totalSales > 0 ? number_format(($monthlySales / $totalSales) * 100, 2) : 0 }}%</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">Total Purchases</td>
                    <td class="px-6 py-4 text-right text-sm text-orange-600 font-bold">₹{{ number_format($totalPurchases, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm text-orange-600 font-bold">₹{{ number_format($monthlyPurchases, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm">{{ $totalPurchases > 0 ? number_format(($monthlyPurchases / $totalPurchases) * 100, 2) : 0 }}%</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">Total Expenses</td>
                    <td class="px-6 py-4 text-right text-sm text-red-600 font-bold">₹{{ number_format($totalExpenses, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm text-red-600 font-bold">₹{{ number_format($monthlyExpenses, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm">{{ $totalExpenses > 0 ? number_format(($monthlyExpenses / $totalExpenses) * 100, 2) : 0 }}%</td>
                </tr>
                <tr class="bg-green-50 hover:bg-green-100">
                    <td class="px-6 py-4 text-sm font-bold text-gray-800">Total Profit</td>
                    <td class="px-6 py-4 text-right text-sm {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">₹{{ number_format($totalProfit, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm {{ $monthlyProfit >= 0 ? 'text-green-600' : 'text-red-600' }} font-bold">₹{{ number_format($monthlyProfit, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold">{{ $totalProfit > 0 ? number_format(($monthlyProfit / $totalProfit) * 100, 2) : 0 }}%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Back Button -->
<div class="mt-6">
    <a href="{{ route('admin.dashboard.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
        ← Back to Dashboard
    </a>
</div>
@endsection

