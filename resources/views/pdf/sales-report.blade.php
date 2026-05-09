<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report — {{ $monthLabel }}</title>
    <style>
        @page { margin: 18mm 10mm 14mm 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #1f2937;
            margin: 0;
        }

        /* Header block (matches the Excel header style) */
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
        .header .period-tag {
            display: inline-block;
            background: #111827;
            color: #fff;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 6px;
        }

        /* Main daily table */
        table.report {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            table-layout: fixed;
        }
        table.report th, table.report td {
            border: 1px solid #d1d5db;
            padding: 3px 4px;
            text-align: right;
        }
        table.report th {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 8px;
            color: #111827;
            vertical-align: bottom;
        }
        /* Vertical headers — stacked characters (one per line). Visually similar to rotated
           Excel headers but renders deterministically in DomPDF without layout drift. */
        table.report th.vert {
            font-size: 7.5px;
            padding: 3px 0 4px 0;
            text-align: center;
            vertical-align: bottom;
            line-height: 1.05;
            white-space: nowrap;
            font-weight: bold;
        }
        table.report td.left, table.report th.left { text-align: left; }
        table.report td.center, table.report th.center { text-align: center; }
        table.report tr.totals-row td {
            font-weight: bold;
            background: #f9fafb;
            border-top: 2px solid #111827;
        }
        /* Text columns: capped width so they wrap instead of forcing the column wide.
           When the cell is empty the column collapses to header width. */
        table.report .notes-cell {
            text-align: left;
            font-size: 7.5px;
            color: #374151;
            white-space: pre-line;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 110px;
        }
        table.report .return-details {
            text-align: left;
            font-size: 7.5px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 60px;
        }
        table.report .expense-details {
            text-align: left;
            font-size: 7.5px;
            white-space: pre-line;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 95px;
        }
        table.report .zero { color: #d1d5db; }

        /* Section title */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #111827;
            background: #f3f4f6;
            padding: 5px 8px;
            margin: 14px 0 6px 0;
            border-left: 4px solid #111827;
        }

        /* Breakdown tables */
        table.breakdown {
            width: 60%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 10px;
        }
        table.breakdown th, table.breakdown td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            text-align: right;
        }
        table.breakdown th {
            background: #f3f4f6;
            font-weight: bold;
        }
        table.breakdown td.product-name {
            text-align: left;
            font-weight: bold;
        }
        table.breakdown tr.subtotal td {
            font-weight: bold;
            background: #fef3c7;
        }

        /* Two-column layout for breakdown sections on page 2 */
        .grid-2 {
            width: 100%;
        }
        .grid-2 .col {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }

        .page-break { page-break-before: always; }

        /* Footer */
        .footer-note {
            margin-top: 8px;
            font-size: 8px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>

{{-- ============== PAGE 1 — Daily breakdown ============== --}}

<div class="header">
    <div class="company">{{ $companyName }}
        <span class="period-tag">{{ $monthLabel }}</span>
    </div>
    <div class="title">Sales Report</div>
    <div class="meta">Period: {{ $periodLabel }} &nbsp;|&nbsp; Generated: {{ $generatedAt }}</div>
</div>

<table class="report">
    {{-- Fixed-layout column widths summing to exactly 100%.
         Non-product widths total 65% — products absorb whatever's left so the table fills
         the page width consistently no matter how many products exist. --}}
    @php
        $productCount     = $products->count();
        $nonProductTotal  = 65; // Date 7 + Online 4 + Cash 4 + Return 3 + Return Details 8 + Total 6 + Notes 14 + Expense 4 + Exp.Detail 11 + Net 4 = 65
        $productSharePct  = 100 - $nonProductTotal; // 35
        $productPct       = $productCount > 0 ? round($productSharePct / $productCount, 3) : 0;
    @endphp
    <colgroup>
        <col style="width: 7%;">                                        {{-- Date --}}
        @foreach ($products as $product)
            <col style="width: {{ $productPct }}%;">
        @endforeach
        <col style="width: 4%;">    {{-- Online --}}
        <col style="width: 4%;">    {{-- Cash --}}
        <col style="width: 3%;">    {{-- Return --}}
        <col style="width: 8%;">    {{-- Return Details --}}
        <col style="width: 6%;">    {{-- Total --}}
        <col style="width: 14%;">   {{-- Notes --}}
        <col style="width: 4%;">    {{-- Expense --}}
        <col style="width: 11%;">   {{-- Exp.Detail --}}
        <col style="width: 4%;">    {{-- Net --}}
    </colgroup>
    <thead>
        <tr>
            <th class="left">Date</th>
            @foreach ($products as $product)
                <th class="vert">{!! implode('<br>', str_split($product->product_code)) !!}</th>
            @endforeach
            <th class="vert">{!! implode('<br>', str_split('Online')) !!}</th>
            <th class="vert">{!! implode('<br>', str_split('Cash')) !!}</th>
            <th class="vert">{!! implode('<br>', str_split('Return')) !!}</th>
            <th class="left">Return Details</th>
            <th>Total</th>
            <th class="left">Notes</th>
            <th>Expense</th>
            <th class="left">Exp.Detail</th>
            <th>Net</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($dailyRows as $row)
            <tr>
                <td class="left">{{ $row['date'] }}</td>
                @foreach ($products as $product)
                    @php $qty = $row['product_qtys'][$product->product_code] ?? 0; @endphp
                    <td class="{{ $qty == 0 ? 'zero' : '' }}">{{ $qty == 0 ? '' : $qty }}</td>
                @endforeach
                <td>{{ $row['online'] > 0 ? number_format($row['online'], 0) : '' }}</td>
                <td>{{ $row['cash'] > 0 ? number_format($row['cash'], 0) : '' }}</td>
                <td>{{ $row['return'] > 0 ? number_format($row['return'], 0) : '' }}</td>
                <td class="return-details">{{ $row['return_details'] }}</td>
                <td>{{ number_format($row['total'], 0) }}</td>
                <td class="notes-cell">{{ $row['notes'] }}</td>
                <td>{{ $row['expense'] > 0 ? number_format($row['expense'], 0) : '' }}</td>
                <td class="expense-details">{{ $row['expense_details'] }}</td>
                <td>{{ number_format($row['final_total'], 0) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ 11 + $products->count() }}" class="center" style="padding: 15px; color: #9ca3af;">
                    No sales in this period.
                </td>
            </tr>
        @endforelse

        @if (count($dailyRows) > 0)
            <tr class="totals-row">
                <td class="left">TOTAL</td>
                @foreach ($products as $product)
                    @php $t = $totals['product'][$product->product_code] ?? 0; @endphp
                    <td>{{ $t == 0 ? '' : $t }}</td>
                @endforeach
                <td>{{ number_format($totals['online'], 0) }}</td>
                <td>{{ number_format($totals['cash'], 0) }}</td>
                <td>{{ $totals['returns'] > 0 ? number_format($totals['returns'], 0) : '' }}</td>
                <td></td>
                <td>{{ number_format(($totals['cash'] + $totals['online']) - $totals['returns'], 0) }}</td>
                <td></td>
                <td>{{ number_format($totals['expense'], 0) }}</td>
                <td></td>
                <td>{{ number_format($totals['grand_total'], 0) }}</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="footer-note">
    Notes column shows follow-up payments received on a date for bills of other dates.
</div>

{{-- ============== PAGE 2 — Product-wise breakdown ============== --}}
<div class="page-break"></div>

<div class="header">
    <div class="company">{{ $companyName }}
        <span class="period-tag">{{ $monthLabel }}</span>
    </div>
    <div class="title">Sales Report — Product-wise Breakdown</div>
    <div class="meta">Period: {{ $periodLabel }} &nbsp;|&nbsp; Generated: {{ $generatedAt }}</div>
</div>

@php
    $hasQty = collect($productPaymentQty)->contains(fn ($r) => array_sum($r) > 0);
    $hasAmt = collect($productPaymentAmount)->contains(fn ($r) => array_sum($r) > 0);
@endphp

<div class="section-title">Quantity by Payment Mode</div>
<table class="breakdown">
    <thead>
        <tr>
            <th class="left">Product</th>
            <th>UPI</th>
            <th>G-PAY</th>
            <th>CASH</th>
            <th>MIX</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            @php
                $row = $productPaymentQty[$product->product_code] ?? ['upi'=>0,'gpay'=>0,'cash'=>0,'mix'=>0];
                $rowTotal = $row['upi'] + $row['gpay'] + $row['cash'] + $row['mix'];
            @endphp
            @if ($rowTotal > 0)
                <tr>
                    <td class="product-name">{{ $product->product_code }}</td>
                    <td>{{ $row['upi'] > 0 ? $row['upi'] : '' }}</td>
                    <td>{{ $row['gpay'] > 0 ? $row['gpay'] : '' }}</td>
                    <td>{{ $row['cash'] > 0 ? $row['cash'] : '' }}</td>
                    <td>{{ $row['mix'] > 0 ? $row['mix'] : '' }}</td>
                    <td><strong>{{ $rowTotal }}</strong></td>
                </tr>
            @endif
        @endforeach
        @if (!$hasQty)
            <tr><td colspan="6" class="center" style="color:#9ca3af; padding:10px;">No data.</td></tr>
        @endif
    </tbody>
</table>

<div class="section-title">Amount by Payment Mode</div>
<table class="breakdown">
    <thead>
        <tr>
            <th class="left">Product</th>
            <th>UPI</th>
            <th>G-PAY</th>
            <th>CASH</th>
            <th>MIX</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            @php
                $row = $productPaymentAmount[$product->product_code] ?? ['upi'=>0,'gpay'=>0,'cash'=>0,'mix'=>0];
                $rowTotal = $row['upi'] + $row['gpay'] + $row['cash'] + $row['mix'];
            @endphp
            @if ($rowTotal > 0)
                <tr>
                    <td class="product-name">{{ $product->product_code }}</td>
                    <td>{{ $row['upi'] > 0 ? number_format($row['upi'], 0) : '' }}</td>
                    <td>{{ $row['gpay'] > 0 ? number_format($row['gpay'], 0) : '' }}</td>
                    <td>{{ $row['cash'] > 0 ? number_format($row['cash'], 0) : '' }}</td>
                    <td>{{ $row['mix'] > 0 ? number_format($row['mix'], 0) : '' }}</td>
                    <td><strong>{{ number_format($rowTotal, 0) }}</strong></td>
                </tr>
            @endif
        @endforeach
        @if (!$hasAmt)
            <tr><td colspan="6" class="center" style="color:#9ca3af; padding:10px;">No data.</td></tr>
        @endif
    </tbody>
</table>

<div class="section-title">Period Summary</div>
<table class="breakdown" style="width: 50%;">
    <tbody>
        <tr>
            <td class="product-name">Total Cash Received</td>
            <td>₹{{ number_format($totals['cash'], 2) }}</td>
        </tr>
        <tr>
            <td class="product-name">Total Online Received</td>
            <td>₹{{ number_format($totals['online'], 2) }}</td>
        </tr>
        <tr>
            <td class="product-name">Total Returns</td>
            <td>₹{{ number_format($totals['returns'], 2) }}</td>
        </tr>
        <tr>
            <td class="product-name">Total Expenses</td>
            <td>₹{{ number_format($totals['expense'], 2) }}</td>
        </tr>
        <tr class="subtotal">
            <td class="product-name">Net (Cash + Online − Returns − Expenses)</td>
            <td>₹{{ number_format($totals['grand_total'], 2) }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>
