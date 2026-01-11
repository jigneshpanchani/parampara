@extends('layouts.admin')

@section('title', 'Purchases')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">🛒 Purchases</h2>
    <a href="{{ route('admin.purchases.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        + Add New Purchase
    </a>
</div>

@if ($purchases->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No purchases found. <a href="{{ route('admin.purchases.create') }}" class="text-blue-500 hover:underline">Create one now</a></p>
    </div>
@else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Due Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->supplier_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->items->count() }} items</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->bill_due_date ? $purchase->bill_due_date->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="text-blue-500 hover:underline mr-3">Edit</a>
                            <form action="{{ route('admin.purchases.destroy', $purchase) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

