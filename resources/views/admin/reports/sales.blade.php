@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">💳 Sales Report</h2>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Total Sales</p>
        <p class="text-3xl font-bold text-gray-800">₹{{ number_format($totalSales, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Total Quantity</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalQuantity }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Amount Paid</p>
        <p class="text-3xl font-bold text-green-600">₹{{ number_format($paidAmount, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Pending Amount</p>
        <p class="text-3xl font-bold text-red-600">₹{{ number_format($pendingAmount, 2) }}</p>
    </div>
</div>

<!-- Sales Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Qty</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Mode</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Paid</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pending</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sells as $sell)
                @foreach($sell->items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sell->sell_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($item->selling_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($item->total_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_mode === 'cash' ? 'bg-blue-100 text-blue-800' : ($sell->payment_mode === 'upi' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                {{ strtoupper($sell->payment_mode) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($sell->payment_status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($sell->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-green-600">₹{{ number_format($sell->amount_paid, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-red-600">₹{{ number_format($sell->pending_amount, 2) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-600">No sales found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection

