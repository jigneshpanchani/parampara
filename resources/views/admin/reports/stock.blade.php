@extends('layouts.admin')

@section('title', 'Stock Report')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📦 Stock Report</h2>
</div>

<!-- Summary Card -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <p class="text-gray-600 text-sm font-semibold">Total Products</p>
    <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
</div>

<!-- Stock Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Code</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total Purchase</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Total Sell</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Return</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Available Stock</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stockData as $data)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <div class="flex items-center">
                            @if ($data['product']->photo)
                                <img src="{{ asset('storage/' . $data['product']->photo) }}" alt="{{ $data['product']->product_name }}" class="h-8 w-8 rounded mr-3">
                            @endif
                            {{ $data['product']->product_name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $data['product']->product_code }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-900 font-semibold">{{ $data['total_purchase'] }}</td>
                    <td class="px-6 py-4 text-sm text-center text-gray-900 font-semibold">{{ $data['total_sell'] }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        <span class="text-gray-900 font-semibold">
                            -{{ $data['purchase_return'] }}
                            @if($data['sell_return'] > 0)
                                +{{ $data['sell_return'] }}
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($data['available_stock'] > 10) bg-green-100 text-green-800
                            @elseif($data['available_stock'] > 0) bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $data['available_stock'] }}
                        </span>
                    </td>
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

