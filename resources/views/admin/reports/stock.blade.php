@extends('layouts.admin')

@section('title', 'Stock Report')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📦 Stock Report</h2>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Total Products</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Total Inventory Value</p>
        <p class="text-3xl font-bold text-gray-800">₹{{ number_format($products->sum(fn($p) => $p->sell_price), 2) }}</p>
    </div>
</div>

<!-- Stock Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Code</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Base Price Range</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sell Price</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Margin</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="flex items-center">
                            @if ($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->product_name }}" class="h-8 w-8 rounded mr-3">
                            @endif
                            {{ $product->product_name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $product->product_code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($product->base_price_min, 2) }} - ₹{{ number_format($product->base_price_max, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($product->sell_price, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @php
                            $margin = $product->base_price_max > 0 ? (($product->sell_price - $product->base_price_max) / $product->base_price_max) * 100 : 0;
                        @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $margin >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ number_format($margin, 2) }}%
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($product->description, 50) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-600">No products found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection

