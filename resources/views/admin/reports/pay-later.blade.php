@extends('layouts.admin')

@section('title', 'Pay Later Customers')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">⏳ Pay Later Customers</h2>
    <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">← Back</a>
</div>

<div class="bg-amber-50 border-l-4 border-amber-400 text-amber-800 text-sm rounded p-4 mb-6">
    Lists every sale that was recorded with an unpaid balance — even if the customer has since cleared it.
    Useful at month-end to see who took anything on credit during the period.
</div>

<!-- Filter and Export -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex gap-4 items-end flex-wrap">
        <form method="GET" action="{{ route('admin.reports.pay-later') }}" class="flex gap-4 items-end flex-wrap flex-1">
            <div class="flex-1 min-w-[180px]">
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $startDate ?? '' }}">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" id="end_date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $endDate ?? '' }}">
            </div>
            <div class="flex-1 min-w-[180px]">
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="" {{ ($status ?? '') === '' ? 'selected' : '' }}>All</option>
                    <option value="outstanding" {{ ($status ?? '') === 'outstanding' ? 'selected' : '' }}>Outstanding (still pending)</option>
                    <option value="cleared" {{ ($status ?? '') === 'cleared' ? 'selected' : '' }}>Cleared (paid back)</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                Filter
            </button>
            <a href="{{ route('admin.reports.pay-later') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                Reset
            </a>
        </form>
        <form action="{{ route('admin.reports.pay-later.export') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <button type="submit" title="Export to XLS"
                class="bg-green-500 hover:bg-green-600 text-white p-2 rounded flex items-center justify-center w-11 h-11 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 3 14 8 19 8"/>
                    <text x="12" y="17" text-anchor="middle" font-size="5.5" font-family="Arial, sans-serif" font-weight="bold" fill="currentColor" stroke="none">XLS</text>
                </svg>
            </button>
        </form>
        <form action="{{ route('admin.reports.pay-later.pdf') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ $startDate }}">
            <input type="hidden" name="end_date" value="{{ $endDate }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <button type="submit" title="Export to PDF"
                class="bg-red-500 hover:bg-red-600 text-white p-2 rounded flex items-center justify-center w-11 h-11 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 3 14 8 19 8"/>
                    <text x="12" y="17" text-anchor="middle" font-size="5.5" font-family="Arial, sans-serif" font-weight="bold" fill="currentColor" stroke="none">PDF</text>
                </svg>
            </button>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">🧾 Pay Later Sales</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totals['sales_count'] }}</p>
        <p class="text-xs text-gray-500 mt-1">
            <span class="text-green-600 font-semibold">{{ $totals['cleared_count'] }} cleared</span>
            &amp;
            <span class="text-red-600 font-semibold">{{ $totals['outstanding_count'] }} outstanding</span>
        </p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">💰 Total Sales Value</p>
        <p class="text-3xl font-bold text-blue-600">₹{{ number_format($totals['total_sales'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">⏳ Original Pay Later</p>
        <p class="text-3xl font-bold text-yellow-600">₹{{ number_format($totals['original_pay_later'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Amount unpaid at sale time</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">❗ Still Outstanding</p>
        <p class="text-3xl font-bold {{ $totals['outstanding_amount'] > 0 ? 'text-red-600' : 'text-green-600' }}">₹{{ number_format($totals['outstanding_amount'], 2) }}</p>
        <p class="text-xs text-gray-500 mt-1">Live pending balance</p>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Sale Date</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Seller</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Contact</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Paid At Sale</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Original Pay Later</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Current Pending</th>
                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Cleared On</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $r)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $r['sale_date']->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <a href="{{ route('admin.sales.show', $r['id']) }}" class="text-blue-600 hover:underline">{{ $r['seller_name'] }}</a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $r['seller_contact'] }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-800">₹{{ number_format($r['total_amount'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right text-blue-600">{{ $r['paid_at_sale'] > 0 ? '₹' . number_format($r['paid_at_sale'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-yellow-700 font-semibold">₹{{ number_format($r['original_pay_later'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right {{ $r['current_pending'] > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">{{ $r['current_pending'] > 0 ? '₹' . number_format($r['current_pending'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                        @if ($r['status'] === 'cleared')
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Cleared</span>
                        @else
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">Outstanding</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $r['cleared_on'] ? $r['cleared_on']->format('d M Y') : '—' }}
                        @if ($r['follow_up_count'] > 0)
                            <span class="text-xs text-gray-400">({{ $r['follow_up_count'] }} payment{{ $r['follow_up_count'] > 1 ? 's' : '' }})</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-600">No pay-later customers in the selected period.</td>
                </tr>
            @endforelse
        </tbody>
        @if($rows->isNotEmpty())
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td class="px-4 py-3 text-sm font-bold text-gray-800" colspan="3">TOTAL ({{ $totals['sales_count'] }} sales)</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">₹{{ number_format($totals['total_sales'], 2) }}</td>
                    <td></td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-yellow-700">₹{{ number_format($totals['original_pay_later'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold {{ $totals['outstanding_amount'] > 0 ? 'text-red-700' : 'text-gray-400' }}">₹{{ number_format($totals['outstanding_amount'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-center font-bold text-gray-700">{{ $totals['cleared_count'] }}/{{ $totals['outstanding_count'] }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection
