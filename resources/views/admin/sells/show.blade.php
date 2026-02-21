@extends('layouts.admin')

@section('title', 'Sale Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Sale Details</h2>
            <a href="{{ route('admin.sells.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Back</a>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-600">Date</p>
                <p class="font-semibold">{{ $sell->sell_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Amount</p>
                <p class="font-semibold">{{ $sell->total_amount_formatted }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Payment Mode</p>
                <p class="font-semibold">{{ $sell->payment_mode_label }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <p class="font-semibold">{{ ucfirst($sell->payment_status) }}</p>
            </div>
        </div>

        <h3 class="text-lg font-semibold mb-3">Items</h3>
        <table class="w-full border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-right">Qty</th>
                    <th class="px-4 py-2 text-right">Price</th>
                    <th class="px-4 py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sell->items as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $item->product->product_name ?? '-' }}</td>
                        <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                        <td class="px-4 py-2 text-right">₹{{ number_format($item->selling_price, 2) }}</td>
                        <td class="px-4 py-2 text-right">₹{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
