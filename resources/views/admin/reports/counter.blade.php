@extends('layouts.admin')

@section('title', 'Counter Report')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">🧾 Counter Report</h2>
    <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">← Back</a>
</div>

<!-- Date Filter and Export -->
@php
    $selectedProductIds = $selectedProductIds ?? [];
    $selectedSet = array_flip($selectedProductIds);
    $productFilterLabel = empty($selectedProductIds)
        ? 'All products'
        : (count($selectedProductIds) . ' selected');
@endphp
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <div class="flex gap-4 items-end flex-wrap">
        <form method="GET" action="{{ route('admin.reports.counter') }}" class="flex gap-4 items-end flex-wrap flex-1">
            <div class="flex-1 min-w-[200px]">
                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $startDate ?? '' }}">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                <input type="date" id="end_date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $endDate ?? '' }}">
            </div>
            <div class="flex-1 min-w-[240px] relative">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Products</label>
                <button type="button" id="productFilterToggle"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span id="productFilterLabel" class="text-gray-700">{{ $productFilterLabel }}</span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div id="productFilterPanel" class="hidden absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-72 overflow-y-auto p-3">
                    <div class="flex items-center justify-between mb-2 pb-2 border-b">
                        <button type="button" id="productSelectAll" class="text-xs text-blue-600 hover:underline">Select all</button>
                        <button type="button" id="productClearAll" class="text-xs text-gray-600 hover:underline">Clear (All)</button>
                    </div>
                    @foreach ($allProducts as $product)
                        <label class="flex items-center gap-2 py-1 cursor-pointer hover:bg-gray-50 px-1 rounded">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}"
                                class="product-checkbox rounded border-gray-300 text-blue-500 focus:ring-blue-500"
                                {{ isset($selectedSet[$product->id]) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $product->product_name }}
                                <span class="text-xs text-gray-400">({{ $product->product_code }})</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                Filter
            </button>
            <a href="{{ route('admin.reports.counter') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                Reset
            </a>
        </form>
        @if($startDate && $endDate)
            <form action="{{ route('admin.reports.counter.export') }}" method="POST">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                @foreach ($selectedProductIds as $pid)
                    <input type="hidden" name="product_ids[]" value="{{ $pid }}">
                @endforeach
                <button type="submit" title="Export to XLS"
                    class="bg-green-500 hover:bg-green-600 text-white p-2 rounded flex items-center justify-center w-11 h-11 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 3 14 8 19 8"/>
                        <text x="12" y="17" text-anchor="middle" font-size="5.5" font-family="Arial, sans-serif" font-weight="bold" fill="currentColor" stroke="none">XLS</text>
                    </svg>
                </button>
            </form>
            <form action="{{ route('admin.reports.counter.pdf') }}" method="POST">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
                @foreach ($selectedProductIds as $pid)
                    <input type="hidden" name="product_ids[]" value="{{ $pid }}">
                @endforeach
                <button type="submit" title="Export to PDF"
                    class="bg-red-500 hover:bg-red-600 text-white p-2 rounded flex items-center justify-center w-11 h-11 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 3 14 8 19 8"/>
                        <text x="12" y="17" text-anchor="middle" font-size="5.5" font-family="Arial, sans-serif" font-weight="bold" fill="currentColor" stroke="none">PDF</text>
                    </svg>
                </button>
            </form>
        @endif
    </div>
</div>

<script>
(function () {
    const toggle = document.getElementById('productFilterToggle');
    const panel  = document.getElementById('productFilterPanel');
    const label  = document.getElementById('productFilterLabel');
    const selectAllBtn = document.getElementById('productSelectAll');
    const clearAllBtn  = document.getElementById('productClearAll');
    const checkboxes = () => document.querySelectorAll('.product-checkbox');

    function updateLabel() {
        const count = Array.from(checkboxes()).filter(c => c.checked).length;
        label.textContent = count === 0 ? 'All products' : (count + ' selected');
    }

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        panel.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!panel.contains(e.target) && e.target !== toggle) {
            panel.classList.add('hidden');
        }
    });

    selectAllBtn.addEventListener('click', () => {
        checkboxes().forEach(c => c.checked = true);
        updateLabel();
    });

    clearAllBtn.addEventListener('click', () => {
        checkboxes().forEach(c => c.checked = false);
        updateLabel();
    });

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('product-checkbox')) updateLabel();
    });
})();
</script>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">💵 Total Cash</p>
        <p class="text-3xl font-bold text-blue-600">₹{{ number_format($totals['cash'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">📱 Total Online</p>
        <p class="text-3xl font-bold text-purple-600">₹{{ number_format($totals['online'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">⏳ Total Pay Later</p>
        <p class="text-3xl font-bold text-yellow-600">₹{{ number_format($totals['pay_later'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">💰 Total Sales</p>
        <p class="text-3xl font-bold text-green-600">₹{{ number_format($totals['sales_amount'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">↩️ Total Returns</p>
        <p class="text-3xl font-bold text-orange-600">₹{{ number_format($totals['return'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">🧾 Total Expense</p>
        <p class="text-3xl font-bold text-red-600">₹{{ number_format($totals['expense'], 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
        <p class="text-gray-600 text-sm font-semibold">📊 Net Total <span class="text-xs font-normal text-gray-500">(Cash + Online − Return − Expense)</span></p>
        <p class="text-3xl font-bold {{ $totals['net'] >= 0 ? 'text-green-700' : 'text-red-700' }}">₹{{ number_format($totals['net'], 2) }}</p>
    </div>
    @if($hasProductFilter ?? false)
        <div class="bg-blue-50 border border-blue-200 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-semibold">💵 Selected Products — Cash</p>
            <p class="text-3xl font-bold text-blue-700">₹{{ number_format($totals['selected_cash'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Proportional share of cash for selected products</p>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-semibold">📱 Selected Products — Online</p>
            <p class="text-3xl font-bold text-purple-700">₹{{ number_format($totals['selected_online'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">Proportional share of online for selected products</p>
        </div>
    @endif
</div>

<!-- Counter Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        @php $colspan = ($hasProductFilter ?? false) ? 10 : 8; @endphp
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Cash</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Online</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Pay Later</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Total Sales</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Return</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Expense</th>
                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Net Total</th>
                @if($hasProductFilter ?? false)
                    <th class="px-4 py-3 text-right text-sm font-semibold text-blue-700 bg-blue-50" title="Selected products' share of cash, allocated proportionally">Sel. Cash</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-purple-700 bg-purple-50" title="Selected products' share of online, allocated proportionally">Sel. Online</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $r)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-900">{{ $r['date'] }}</td>
                    <td class="px-4 py-3 text-sm text-right text-blue-600">{{ $r['cash'] > 0 ? '₹' . number_format($r['cash'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-purple-600">{{ $r['online'] > 0 ? '₹' . number_format($r['online'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-yellow-700">{{ $r['pay_later'] > 0 ? '₹' . number_format($r['pay_later'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-gray-800">{{ $r['sales_amount'] > 0 ? '₹' . number_format($r['sales_amount'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-orange-600">{{ $r['return'] > 0 ? '₹' . number_format($r['return'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-600">{{ $r['expense'] > 0 ? '₹' . number_format($r['expense'], 2) : '—' }}</td>
                    <td class="px-4 py-3 text-sm text-right font-semibold {{ $r['net'] >= 0 ? 'text-green-700' : 'text-red-700' }}">₹{{ number_format($r['net'], 2) }}</td>
                    @if($hasProductFilter ?? false)
                        <td class="px-4 py-3 text-sm text-right text-blue-700 bg-blue-50/40 font-semibold">{{ $r['selected_cash'] > 0 ? '₹' . number_format($r['selected_cash'], 2) : '—' }}</td>
                        <td class="px-4 py-3 text-sm text-right text-purple-700 bg-purple-50/40 font-semibold">{{ $r['selected_online'] > 0 ? '₹' . number_format($r['selected_online'], 2) : '—' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $colspan }}" class="px-4 py-3 text-center text-gray-600">No data found for the selected period.</td>
                </tr>
            @endforelse
        </tbody>
        @if(!empty($rows))
            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                <tr>
                    <td class="px-4 py-3 text-sm font-bold text-gray-800">TOTAL</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-blue-700">₹{{ number_format($totals['cash'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-purple-700">₹{{ number_format($totals['online'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-yellow-700">₹{{ number_format($totals['pay_later'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-gray-800">₹{{ number_format($totals['sales_amount'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-orange-700">₹{{ number_format($totals['return'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold text-red-700">₹{{ number_format($totals['expense'], 2) }}</td>
                    <td class="px-4 py-3 text-sm text-right font-bold {{ $totals['net'] >= 0 ? 'text-green-800' : 'text-red-800' }}">₹{{ number_format($totals['net'], 2) }}</td>
                    @if($hasProductFilter ?? false)
                        <td class="px-4 py-3 text-sm text-right font-bold text-blue-800 bg-blue-50">₹{{ number_format($totals['selected_cash'], 2) }}</td>
                        <td class="px-4 py-3 text-sm text-right font-bold text-purple-800 bg-purple-50">₹{{ number_format($totals['selected_online'], 2) }}</td>
                    @endif
                </tr>
            </tfoot>
        @endif
    </table>
</div>

<div class="mt-6">
    <a href="{{ route('admin.reports.index') }}" class="text-blue-500 hover:underline">← Back to Reports</a>
</div>
@endsection
