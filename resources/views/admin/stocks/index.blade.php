@extends('layouts.admin')

@section('title', 'Stock Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-800">Stock List</h2>
        <a href="{{ route('admin.stocks.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            + Add New Stock
        </a>
    </div>

    <!-- Stocks Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($stocks->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Created At</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($stocks as $stock)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stock->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $stock->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">${{ number_format($stock->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($stock->status === 'in_stock') bg-green-100 text-green-800
                                    @elseif($stock->status === 'low_stock') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $stock->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $stock->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm flex gap-3 justify-center">
                                <a href="{{ route('admin.stocks.edit', $stock->id) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.stocks.destroy', $stock->id) }}" method="POST" class="inline delete-form" data-item-name="Stock: {{ $stock->product_name }}">
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
        @else
            <div class="p-6 text-center text-gray-500">
                <p>No stocks found. <a href="{{ route('admin.stocks.create') }}" class="text-blue-600 hover:text-blue-800">Create one now</a></p>
            </div>
        @endif
    </div>
</div>
@endsection

