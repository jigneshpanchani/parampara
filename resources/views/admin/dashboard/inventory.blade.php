@extends('layouts.admin')

@section('title', 'Inventory Overview')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📊 Inventory Overview</h2>
    <p class="text-gray-600 mt-2">Monitor your stock levels and inventory value</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Products -->
    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow p-6 border-l-4 border-purple-500">
        <p class="text-gray-600 text-sm font-semibold">Total Products</p>
        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalProducts }}</p>
        <p class="text-xs text-gray-500 mt-2">Active products</p>
    </div>

    <!-- Low Stock Products -->
    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow p-6 border-l-4 border-yellow-500">
        <p class="text-gray-600 text-sm font-semibold">Low Stock</p>
        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ count($lowStockProducts) }}</p>
        <p class="text-xs text-gray-500 mt-2">Less than 10 units</p>
    </div>

    <!-- Out of Stock -->
    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow p-6 border-l-4 border-red-500">
        <p class="text-gray-600 text-sm font-semibold">Out of Stock</p>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ $outOfStockProducts }}</p>
        <p class="text-xs text-gray-500 mt-2">Zero quantity</p>
    </div>

    <!-- Inventory Value -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-gray-600 text-sm font-semibold">Inventory Value</p>
        <p class="text-3xl font-bold text-green-600 mt-2">₹{{ number_format($totalInventoryValue, 2) }}</p>
        <p class="text-xs text-gray-500 mt-2">Total cost value</p>
    </div>
</div>

<!-- Low Stock Products Table -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-xl font-bold text-gray-800 mb-6">⚠️ Low Stock Products</h3>
    
    @if(count($lowStockProducts) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-yellow-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">SKU</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Current Stock</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Cost Price</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Stock Value</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($lowStockProducts as $product)
                        <tr class="hover:bg-yellow-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $product->sku ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold {{ $product->quantity == 0 ? 'text-red-600' : 'text-yellow-600' }}">{{ $product->quantity }}</td>
                            <td class="px-6 py-4 text-right text-sm text-gray-600">₹{{ number_format($product->cost_price, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">₹{{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($product->quantity == 0)
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Out of Stock</span>
                                @else
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Low Stock</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-500 text-lg">✅ All products have healthy stock levels!</p>
        </div>
    @endif
</div>

<!-- Stock Distribution -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Stock Categories -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Stock Distribution</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-gray-700 font-semibold">Healthy Stock (>50)</span>
                </div>
                <span class="text-2xl font-bold text-green-600">
                    {{ $totalProducts > 0 ? count(array_filter($lowStockProducts->toArray(), fn($p) => $p['quantity'] > 50)) : 0 }}
                </span>
            </div>
            <div class="flex items-center justify-between pb-4 border-b">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-gray-700 font-semibold">Low Stock (1-50)</span>
                </div>
                <span class="text-2xl font-bold text-yellow-600">{{ count($lowStockProducts) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                    <span class="text-gray-700 font-semibold">Out of Stock (0)</span>
                </div>
                <span class="text-2xl font-bold text-red-600">{{ $outOfStockProducts }}</span>
            </div>
        </div>
    </div>

    <!-- Inventory Insights -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Inventory Insights</h3>
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Average Stock Value per Product</p>
                <p class="text-2xl font-bold text-blue-600">₹{{ number_format($totalInventoryValue / max($totalProducts, 1), 2) }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Stock Health</p>
                <p class="text-2xl font-bold text-purple-600">
                    {{ $totalProducts > 0 ? number_format(((($totalProducts - count($lowStockProducts)) / $totalProducts) * 100), 1) : 0 }}%
                </p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-1">Products Needing Attention</p>
                <p class="text-2xl font-bold text-green-600">{{ count($lowStockProducts) + $outOfStockProducts }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex gap-4">
    <a href="{{ route('admin.products.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
        📦 Manage Products
    </a>
    <a href="{{ route('admin.stocks.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-6 rounded">
        📊 Stock Management
    </a>
    <a href="{{ route('admin.dashboard.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
        ← Back to Dashboard
    </a>
</div>
@endsection

