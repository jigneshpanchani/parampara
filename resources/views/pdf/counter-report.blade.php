<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Counter Report — {{ $monthLabel }}</title>
    <style>
        @page { margin: 18mm 12mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
        }
        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header .company {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
        }
        .header .title {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
            margin-top: 2px;
        }
        .header .meta {
            font-size: 9px;
            color: #6b7280;
            margin-top: 4px;
        }
        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .summary td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            width: 25%;
            vertical-align: top;
        }
        .summary .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary .value {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
        }
        .v-blue   { color: #1d4ed8; }
        .v-purple { color: #7e22ce; }
        .v-yellow { color: #a16207; }
        .v-green  { color: #15803d; }
        .v-orange { color: #c2410c; }
        .v-red    { color: #b91c1c; }
        .v-gray   { color: #1f2937; }
        .net-pos  { color: #15803d; }
        .net-neg  { color: #b91c1c; }

        table.report {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        table.report th, table.report td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
        }
        table.report thead th {
            background: #f3f4f6;
            font-weight: bold;
            color: #111827;
            text-align: center;
        }
        table.report td.date { text-align: left; }
        table.report td.num  { text-align: right; }
        table.report tfoot td {
            background: #e5e7eb;
            font-weight: bold;
        }
        .empty {
            text-align: center;
            padding: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">{{ $companyName }}</div>
        <div class="title">Counter Report</div>
        <div class="meta">Period: {{ $periodLabel }} &nbsp;|&nbsp; Generated: {{ $generatedAt }}</div>
        <div class="meta">Products: {{ $productLabel ?? 'All products' }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Total Cash</div>
                <div class="value v-blue">₹{{ number_format($totals['cash'], 2) }}</div>
            </td>
            <td>
                <div class="label">Total Online</div>
                <div class="value v-purple">₹{{ number_format($totals['online'], 2) }}</div>
            </td>
            <td>
                <div class="label">Total Pay Later</div>
                <div class="value v-yellow">₹{{ number_format($totals['pay_later'], 2) }}</div>
            </td>
            <td>
                <div class="label">Total Sales</div>
                <div class="value v-green">₹{{ number_format($totals['sales_amount'], 2) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Total Returns</div>
                <div class="value v-orange">₹{{ number_format($totals['return'], 2) }}</div>
            </td>
            <td>
                <div class="label">Total Expense</div>
                <div class="value v-red">₹{{ number_format($totals['expense'], 2) }}</div>
            </td>
            <td colspan="2">
                <div class="label">Net Total (Cash + Online − Return − Expense)</div>
                <div class="value {{ $totals['net'] >= 0 ? 'net-pos' : 'net-neg' }}">₹{{ number_format($totals['net'], 2) }}</div>
            </td>
        </tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th>Date</th>
                <th>Cash</th>
                <th>Online</th>
                <th>Pay Later</th>
                <th>Total Sales</th>
                <th>Return</th>
                <th>Expense</th>
                <th>Net Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $r)
                <tr>
                    <td class="date">{{ $r['date'] }}</td>
                    <td class="num">{{ $r['cash'] > 0 ? number_format($r['cash'], 2) : '—' }}</td>
                    <td class="num">{{ $r['online'] > 0 ? number_format($r['online'], 2) : '—' }}</td>
                    <td class="num">{{ $r['pay_later'] > 0 ? number_format($r['pay_later'], 2) : '—' }}</td>
                    <td class="num">{{ $r['sales_amount'] > 0 ? number_format($r['sales_amount'], 2) : '—' }}</td>
                    <td class="num">{{ $r['return'] > 0 ? number_format($r['return'], 2) : '—' }}</td>
                    <td class="num">{{ $r['expense'] > 0 ? number_format($r['expense'], 2) : '—' }}</td>
                    <td class="num {{ $r['net'] >= 0 ? 'net-pos' : 'net-neg' }}">{{ number_format($r['net'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">No data for the selected period.</td></tr>
            @endforelse
        </tbody>
        @if(!empty($rows))
            <tfoot>
                <tr>
                    <td>TOTAL</td>
                    <td class="num">{{ number_format($totals['cash'], 2) }}</td>
                    <td class="num">{{ number_format($totals['online'], 2) }}</td>
                    <td class="num">{{ number_format($totals['pay_later'], 2) }}</td>
                    <td class="num">{{ number_format($totals['sales_amount'], 2) }}</td>
                    <td class="num">{{ number_format($totals['return'], 2) }}</td>
                    <td class="num">{{ number_format($totals['expense'], 2) }}</td>
                    <td class="num {{ $totals['net'] >= 0 ? 'net-pos' : 'net-neg' }}">{{ number_format($totals['net'], 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
