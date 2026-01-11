@extends('layouts.admin')

@section('title', 'Purchases Report')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📋 Purchases Report</h2>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Total Purchases</p>
        <p class="text-3xl font-bold text-gray-800">₹{{ number_format($totalPurchases, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Total Items</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalItems }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Avg Purchase Value</p>
        <p class="text-3xl font-bold text-gray-800">₹{{ number_format($purchases->count() > 0 ? $totalPurchases / $purchases->count() : 0, 2) }}</p>
    </div>
</div>

<!-- Purchases Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Transport</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Due Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchases as $purchase)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $purchase->purchase_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->supplier_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->items->count() }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($purchase->items->sum('total_price'), 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($purchase->transportation_cost, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">₹{{ number_format($purchase->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->bill_due_date ? $purchase->bill_due_date->format('d M Y') : '-' }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $purchase->status === 'completed' ? 'bg-green-100 text-green-800' : ($purchase->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($purchase->status) }}
                        </span>
                    </td>
                </tr>
                @if ($purchase->items->count() > 0)
                    <tr class="bg-gray-50">
                        <td colspan="9" class="px-6 py-3">
                            <div class="text-sm">
                                <strong>Items:</strong>
                                @foreach ($purchase->items as $item)
                                    <div class="ml-4">{{ $item->product->product_name }} - {{ $item->quantity }} × ₹{{ number_format($item->purchase_price, 2) }} = ₹{{ number_format($item->total_price, 2) }}</div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-600">No purchases found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection

