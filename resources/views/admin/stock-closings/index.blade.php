@extends('layouts.admin')

@section('title', 'Stock Closing Verification')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Stock Closing Verification</h2>
            <div class="flex gap-2">
                <button type="button" onclick="openSaveClosingModal()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">+ Save Closing</button>
                <a href="{{ route('admin.stock-closings.history') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">History</a>
                <a href="{{ route('admin.stock.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">← Back</a>
            </div>
        </div>

        {{-- Date range filter --}}
        <form method="GET" action="{{ route('admin.stock-closings.index') }}" class="mb-6 flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date *</label>
                <input type="date" name="start_date" value="{{ $startDate }}" required
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Date *</label>
                <input type="date" name="end_date" value="{{ $endDate }}" required
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Load Data
            </button>
        </form>

        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded text-sm text-blue-700">
            Showing data from <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong>
            to <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>.
            Enter <strong>Opening</strong> qty (closing stock just before {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }})
            and <strong>Closing</strong> qty (stock on {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}).
            <br>
            <code class="bg-white px-1 rounded">Expected = Opening + Purchase − Purchase Return − Sell + Sell Return</code>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-2 text-left font-semibold">Product</th>
                        <th class="px-2 py-2 text-right font-semibold text-gray-700" title="Closing stock just before start date">Opening</th>
                        <th class="px-2 py-2 text-right font-semibold text-green-700">Purchase</th>
                        <th class="px-2 py-2 text-right font-semibold text-orange-600">Pur. Return</th>
                        <th class="px-2 py-2 text-right font-semibold text-red-600">Sell</th>
                        <th class="px-2 py-2 text-right font-semibold text-yellow-700">Sell Return</th>
                        <th class="px-2 py-2 text-right font-semibold text-blue-700">Expected</th>
                        <th class="px-2 py-2 text-right font-semibold text-purple-700" title="Stock on end date">Closing</th>
                        <th class="px-2 py-2 text-right font-semibold">Difference</th>
                        <th class="px-2 py-2 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="border-b hover:bg-gray-50 closing-row">
                        <td class="px-2 py-2 font-medium text-gray-800">{{ $row['product_name'] }}</td>
                        <td class="px-2 py-2">
                            <input type="number" step="0.01" min="0" placeholder="0"
                                class="w-20 px-2 py-1 border border-gray-300 rounded text-right focus:outline-none focus:ring-2 focus:ring-gray-400 opening-input">
                        </td>
                        <td class="px-2 py-2 text-right text-green-700 purchased-cell" data-value="{{ $row['purchased_qty'] }}">{{ number_format($row['purchased_qty'], 2) }}</td>
                        <td class="px-2 py-2 text-right text-orange-600 pur-ret-cell" data-value="{{ $row['purchase_returns_qty'] }}">{{ number_format($row['purchase_returns_qty'], 2) }}</td>
                        <td class="px-2 py-2 text-right text-red-600 sold-cell" data-value="{{ $row['sold_qty'] }}">{{ number_format($row['sold_qty'], 2) }}</td>
                        <td class="px-2 py-2 text-right text-yellow-700 sell-ret-cell" data-value="{{ $row['sell_returns_qty'] }}">{{ number_format($row['sell_returns_qty'], 2) }}</td>
                        <td class="px-2 py-2 text-right font-semibold text-blue-700 expected-cell">0.00</td>
                        <td class="px-2 py-2">
                            <input type="number" step="0.01" min="0" placeholder="0"
                                class="w-20 px-2 py-1 border border-gray-300 rounded text-right focus:outline-none focus:ring-2 focus:ring-purple-400 closing-input">
                        </td>
                        <td class="px-2 py-2 text-right font-bold diff-cell text-gray-400">—</td>
                        <td class="px-2 py-2 text-center status-cell"><span class="text-gray-400">—</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function num(v) { return parseFloat(v) || 0; }

document.querySelectorAll('.closing-row').forEach(row => {
    const openingInput = row.querySelector('.opening-input');
    const closingInput = row.querySelector('.closing-input');
    const expectedCell = row.querySelector('.expected-cell');
    const diffCell     = row.querySelector('.diff-cell');
    const statusCell   = row.querySelector('.status-cell');
    const purchased    = num(row.querySelector('.purchased-cell').dataset.value);
    const purRet       = num(row.querySelector('.pur-ret-cell').dataset.value);
    const sold         = num(row.querySelector('.sold-cell').dataset.value);
    const sellRet      = num(row.querySelector('.sell-ret-cell').dataset.value);

    function recalc() {
        const opening  = num(openingInput.value);
        const expected = opening + purchased - purRet - sold + sellRet;
        expectedCell.textContent = expected.toFixed(2);

        if (closingInput.value === '') {
            diffCell.textContent = '—';
            diffCell.className   = 'px-2 py-2 text-right font-bold diff-cell text-gray-400';
            statusCell.innerHTML = '<span class="text-gray-400">—</span>';
            row.classList.remove('bg-red-50', 'bg-green-50');
            return;
        }

        const closing = num(closingInput.value);
        const diff    = expected - closing;

        if (diff > 0) {
            diffCell.textContent = '−' + diff.toFixed(2);
            diffCell.className   = 'px-2 py-2 text-right font-bold diff-cell text-red-600';
            statusCell.innerHTML = '<span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded">Missing</span>';
            row.classList.add('bg-red-50');
            row.classList.remove('bg-green-50');
        } else if (diff < 0) {
            diffCell.textContent = '+' + Math.abs(diff).toFixed(2);
            diffCell.className   = 'px-2 py-2 text-right font-bold diff-cell text-green-600';
            statusCell.innerHTML = '<span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">Surplus</span>';
            row.classList.add('bg-green-50');
            row.classList.remove('bg-red-50');
        } else {
            diffCell.textContent = '0.00';
            diffCell.className   = 'px-2 py-2 text-right font-bold diff-cell text-gray-500';
            statusCell.innerHTML = '<span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded">OK</span>';
            row.classList.remove('bg-red-50', 'bg-green-50');
        }
    }

    openingInput.addEventListener('input', recalc);
    closingInput.addEventListener('input', recalc);
    recalc();
});
</script>

@include('admin.stock-closings._save_modal')
@endsection
