@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">💰 Sales</h2>
    <a href="{{ route('admin.sells.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        + Record New Sale
    </a>
</div>

@if ($sells->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No sales found. <a href="{{ route('admin.sells.create') }}" class="text-blue-500 hover:underline">Record one now</a></p>
    </div>
@else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Qty</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Mode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pending</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sells as $sell)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sell->sell_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sell->seller_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @foreach($sell->items as $item)
                                <div>{{ $item->product->product_name }}</div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @foreach($sell->items as $item)
                                <div>{{ $item->quantity }}</div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @foreach($sell->items as $item)
                                <div>₹{{ number_format($item->selling_price, 2, '.', '') }}</div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($sell->total_amount, 2, '.', '') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_mode === 'cash' ? 'bg-blue-100 text-blue-800' : ($sell->payment_mode === 'upi' ? 'bg-purple-100 text-purple-800' : ($sell->payment_mode === 'qr' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800')) }}">
                                {{ strtoupper($sell->payment_mode) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($sell->payment_status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($sell->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-red-600">₹{{ number_format($sell->pending_amount, 2, '.', '') }}</td>
                        <td class="px-6 py-4 text-sm flex gap-3 justify-center">
                            <a href="{{ route('admin.sells.edit', $sell) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.sells.destroy', $sell) }}" method="POST" class="inline delete-form" data-item-name="Sale #{{ $sell->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

