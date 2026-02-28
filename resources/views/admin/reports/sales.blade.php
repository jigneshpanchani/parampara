@extends('layouts.admin')

@section('title', 'Sales Report')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">💳 Sales Report</h2>
</div>

<!-- Date Filter and Export -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex gap-4 items-end flex-wrap">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="flex gap-4 items-end flex-wrap flex-1">
            <div class="flex-1 min-w-[200px]">
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $startDate ?? '' }}">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" id="end_date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $endDate ?? '' }}">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="payment_mode" class="block text-sm font-semibold text-gray-700 mb-2">Payment Mode</label>
                <select id="payment_mode" name="payment_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="cash" {{ ($paymentMode ?? '') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="upi" {{ ($paymentMode ?? '') === 'upi' ? 'selected' : '' }}>UPI</option>
                    <option value="gpay" {{ ($paymentMode ?? '') === 'gpay' ? 'selected' : '' }}>G-pay</option>
                    <option value="mix" {{ ($paymentMode ?? '') === 'mix' ? 'selected' : '' }}>Mix</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                Filter
            </button>
            <a href="{{ route('admin.reports.sales') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                Reset
            </a>
        </form>
        @if($startDate && $endDate)
            <form action="{{ route('admin.reports.sales.export') }}" method="POST">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded flex items-center gap-2">
                    📥 Export to Excel
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">💵 Total Cash Sales</p>
        <p class="text-3xl font-bold text-blue-600">₹{{ number_format($cashSales, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">📱 Total Online Sales</p>
        <p class="text-3xl font-bold text-purple-600">₹{{ number_format($onlineSales, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">🔄 Mix Sales</p>
        <p class="text-3xl font-bold text-indigo-600">₹{{ number_format($mixSales, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 relative group">
        <p class="text-gray-600 text-sm font-semibold">📊 Total Quantity</p>
        <p class="text-3xl font-bold text-gray-800 cursor-help">{{ $totalQuantity }}</p>

        <!-- Tooltip -->
        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-2 px-3 whitespace-nowrap z-10">
            @if(count($quantityByProduct) > 0)
                @foreach($quantityByProduct as $productCode => $qty)
                    <div>{{ $productCode }}: {{ $qty }}</div>
                @endforeach
            @else
                <div>No products sold</div>
            @endif
            <!-- Arrow -->
            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">💰 Total Sales</p>
        <p class="text-3xl font-bold text-green-600">₹{{ number_format($totalSales, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">🧾 Total Expenses</p>
        <p class="text-3xl font-bold text-red-600">₹{{ number_format($totalExpenses, 2) }}</p>
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
            </tr>
        </thead>
        <tbody>
            @forelse ($sells as $sell)
                @foreach($sell->items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sell->sell_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product?->product_name ?? 'Deleted Product' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($item->selling_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($item->total_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($sell->payment_mode === 'cash')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">CASH</span>
                            @elseif($sell->payment_mode === 'upi')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-800">UPI</span>
                            @elseif($sell->payment_mode === 'gpay')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">G-PAY</span>
                            @elseif($sell->payment_mode === 'mix')
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">MIX</span>
                                @if($sell->cash_amount > 0 || $sell->online_amount > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        <span class="text-blue-600">₹{{ number_format($sell->cash_amount, 2) }}</span> |
                                        <span class="text-purple-600">₹{{ number_format($sell->online_amount, 2) }}</span>
                                    </div>
                                @endif
                            @else
                                <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">{{ strtoupper($sell->payment_mode) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($sell->payment_status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($sell->payment_status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-600">No sales found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection

