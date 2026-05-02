@extends('layouts.admin')

@section('title', 'Purchases Report')

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <h2 class="text-3xl font-bold text-gray-800">📋 Purchases Report</h2>
    <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">← Back</a>
</div>

<!-- Filters & Export -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex gap-4 items-end flex-wrap">
        <form method="GET" action="{{ route('admin.reports.purchases') }}" class="flex gap-4 items-end flex-wrap flex-1">
            <div class="flex-1 min-w-[160px]">
                <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                <input type="date" id="date_from" name="date_from" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $dateFrom }}">
            </div>
            <div class="flex-1 min-w-[160px]">
                <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                <input type="date" id="date_to" name="date_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $dateTo }}">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label for="paid_status" class="block text-sm font-semibold text-gray-700 mb-2">Paid Status</label>
                <select id="paid_status" name="paid_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="paid" {{ ($paidStatus ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ ($paidStatus ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="pending" {{ ($paidStatus ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                Filter
            </button>
            <a href="{{ route('admin.reports.purchases') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded inline-flex items-center">
                Reset
            </a>
        </form>
        @php
            $exportQuery = array_filter(request()->only(['date_from', 'date_to', 'paid_status']), fn ($v) => $v !== null && $v !== '');
            $exportUrl = route('admin.reports.purchases.export') . (count($exportQuery) ? '?' . http_build_query($exportQuery) : '');
        @endphp
        <a href="{{ $exportUrl }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded flex items-center gap-2 whitespace-nowrap">
            📥 Export to Excel
        </a>
    </div>
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
                        @php $payStatus = $purchase->getPaymentStatus(); @endphp
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $payStatus === 'paid' ? 'bg-green-100 text-green-800' : ($payStatus === 'partial' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($payStatus) }}
                        </span>
                    </td>
                </tr>
                @if ($purchase->items->count() > 0)
                    <tr class="bg-gray-50">
                        <td colspan="8" class="px-6 py-3">
                            <div class="text-sm">
                                <strong>Items:</strong>
                                @foreach ($purchase->items as $item)
                                    <div class="ml-4">{{ $item->product?->product_name ?? 'Deleted Product' }} - {{ $item->quantity }} × ₹{{ number_format($item->purchase_price, 2) }} = ₹{{ number_format($item->total_price, 2) }}</div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-600">No purchases found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection

