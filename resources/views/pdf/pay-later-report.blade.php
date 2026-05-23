<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay Later Customers — {{ $monthLabel }}</title>
    <style>
        @page { margin: 14mm 10mm 12mm 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1f2937;
            margin: 0;
        }
        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .header .company { font-size: 16px; font-weight: bold; color: #111827; }
        .header .title   { font-size: 13px; font-weight: bold; color: #374151; margin-top: 2px; }
        .header .meta    { font-size: 9px;  color: #6b7280; margin-top: 4px; }

        .summary { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .summary td { border: 1px solid #d1d5db; padding: 6px 8px; width: 25%; vertical-align: top; }
        .summary .label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary .value { font-size: 12px; font-weight: bold; margin-top: 2px; }
        .v-blue { color: #1d4ed8; }
        .v-yellow { color: #a16207; }
        .v-red    { color: #b91c1c; }
        .v-green  { color: #15803d; }
        .v-gray   { color: #1f2937; }

        table.report { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        table.report th, table.report td { border: 1px solid #d1d5db; padding: 3px 5px; }
        table.report thead th { background: #f3f4f6; font-weight: bold; color: #111827; text-align: center; }
        table.report td.left  { text-align: left; }
        table.report td.right { text-align: right; }
        table.report td.center { text-align: center; }
        table.report tfoot td { background: #e5e7eb; font-weight: bold; }
        .pill-cleared { background: #dcfce7; color: #166534; padding: 1px 5px; border-radius: 3px; font-weight: bold; }
        .pill-out     { background: #fee2e2; color: #991b1b; padding: 1px 5px; border-radius: 3px; font-weight: bold; }
        .empty { text-align: center; padding: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company">{{ $companyName }}</div>
        <div class="title">Pay Later Customers</div>
        <div class="meta">Period: {{ $periodLabel }} &nbsp;|&nbsp; Status: {{ $statusLabel }} &nbsp;|&nbsp; Generated: {{ $generatedAt }}</div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Pay Later Sales</div>
                <div class="value v-gray">{{ $totals['sales_count'] }}</div>
                <div style="font-size:8px; color:#6b7280; margin-top:2px;">
                    {{ $totals['cleared_count'] }} cleared / {{ $totals['outstanding_count'] }} outstanding
                </div>
            </td>
            <td>
                <div class="label">Total Sales Value</div>
                <div class="value v-blue">₹{{ number_format($totals['total_sales'], 2) }}</div>
            </td>
            <td>
                <div class="label">Original Pay Later</div>
                <div class="value v-yellow">₹{{ number_format($totals['original_pay_later'], 2) }}</div>
            </td>
            <td>
                <div class="label">Still Outstanding</div>
                <div class="value {{ $totals['outstanding_amount'] > 0 ? 'v-red' : 'v-green' }}">₹{{ number_format($totals['outstanding_amount'], 2) }}</div>
            </td>
        </tr>
    </table>

    <table class="report">
        <thead>
            <tr>
                <th>Sale Date</th>
                <th>Seller</th>
                <th>Contact</th>
                <th>Total</th>
                <th>Paid At Sale</th>
                <th>Original Pay Later</th>
                <th>Current Pending</th>
                <th>Status</th>
                <th>Cleared On</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $r)
                <tr>
                    <td class="left">{{ $r['sale_date']->format('d M Y') }}</td>
                    <td class="left">{{ $r['seller_name'] }}</td>
                    <td class="left">{{ $r['seller_contact'] }}</td>
                    <td class="right">{{ number_format($r['total_amount'], 2) }}</td>
                    <td class="right">{{ $r['paid_at_sale'] > 0 ? number_format($r['paid_at_sale'], 2) : '—' }}</td>
                    <td class="right">{{ number_format($r['original_pay_later'], 2) }}</td>
                    <td class="right">{{ $r['current_pending'] > 0 ? number_format($r['current_pending'], 2) : '—' }}</td>
                    <td class="center"><span class="{{ $r['status'] === 'cleared' ? 'pill-cleared' : 'pill-out' }}">{{ ucfirst($r['status']) }}</span></td>
                    <td class="left">{{ $r['cleared_on'] ? $r['cleared_on']->format('d M Y') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="empty">No pay-later customers in the selected period.</td></tr>
            @endforelse
        </tbody>
        @if($rows->isNotEmpty())
            <tfoot>
                <tr>
                    <td class="left" colspan="3">TOTAL ({{ $totals['sales_count'] }} sales)</td>
                    <td class="right">{{ number_format($totals['total_sales'], 2) }}</td>
                    <td></td>
                    <td class="right">{{ number_format($totals['original_pay_later'], 2) }}</td>
                    <td class="right">{{ number_format($totals['outstanding_amount'], 2) }}</td>
                    <td class="center">{{ $totals['cleared_count'] }} / {{ $totals['outstanding_count'] }}</td>
                    <td></td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
