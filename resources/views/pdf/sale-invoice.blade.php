<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $saleInvoice->invoice_number }}</title>
    <style>
        @page { margin: 14mm 12mm 14mm 12mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
        }

        /* Company header */
        .company-block {
            text-align: center;
            border-bottom: 2px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-block .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
            letter-spacing: 0.5px;
        }
        .company-block .company-meta {
            font-size: 9px;
            color: #4b5563;
            margin-top: 2px;
        }
        .company-block .gst {
            font-size: 9px;
            color: #111827;
            font-weight: bold;
            margin-top: 2px;
        }

        /* Invoice title bar */
        .invoice-title-bar {
            background: #111827;
            color: #fff;
            text-align: center;
            padding: 6px;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        /* Meta grid */
        table.meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
        }
        table.meta-grid td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
            vertical-align: top;
        }
        table.meta-grid .label {
            color: #6b7280;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }
        table.meta-grid .value {
            font-weight: bold;
            color: #111827;
            font-size: 11px;
        }
        .pill {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .pill-cash { background: #dbeafe; color: #1e40af; }
        .pill-online { background: #ede9fe; color: #6b21a8; }
        .pill-mix { background: #e0e7ff; color: #3730a3; }

        /* Items table */
        table.items {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-bottom: 10px;
        }
        table.items th, table.items td {
            border: 1px solid #d1d5db;
            padding: 4px 5px;
        }
        table.items th {
            background: #111827;
            color: #fff;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        table.items td { vertical-align: top; }
        table.items td.num { text-align: right; }
        table.items td.center { text-align: center; }
        table.items tr.subtotal td {
            background: #f3f4f6;
            font-weight: bold;
            font-style: italic;
        }
        table.items tr.grand-total td {
            background: #111827;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }

        /* Summary cards */
        table.summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 12px;
        }
        table.summary td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: center;
            width: 25%;
        }
        table.summary .label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        table.summary .value {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }
        table.summary .value.cash { color: #1e40af; }
        table.summary .value.online { color: #6b21a8; }

        /* Notes box */
        .notes-box {
            border: 1px solid #d1d5db;
            background: #fffbeb;
            padding: 6px 8px;
            font-size: 9px;
            margin-bottom: 10px;
        }
        .notes-box .notes-label {
            font-weight: bold;
            color: #92400e;
            margin-right: 4px;
        }

        /* Footer */
        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #d1d5db;
            font-size: 8px;
            color: #6b7280;
            text-align: center;
            font-style: italic;
        }
        .signature-row {
            margin-top: 30px;
            width: 100%;
        }
        .signature-row td {
            text-align: center;
            font-size: 9px;
            color: #4b5563;
            padding-top: 30px;
            border-top: 1px solid #6b7280;
            width: 33%;
        }
    </style>
</head>
<body>

@php
    $company = $company ?? null;
    $companyName = $company?->company_name ?: 'Parampara';
    $type = $saleInvoice->invoice_type;
    $pillClass = match ($type) {
        \App\Models\SaleInvoice::TYPE_ONLINE => 'pill-online',
        \App\Models\SaleInvoice::TYPE_MIX => 'pill-mix',
        default => 'pill-cash',
    };
@endphp

<div class="company-block">
    <div class="company-name">{{ strtoupper($companyName) }}</div>
    @if ($company)
        @if ($company->address)
            <div class="company-meta">{{ $company->address }}</div>
        @endif
        <div class="company-meta">
            @if ($company->phone) Phone: {{ $company->phone }} @endif
            @if ($company->phone && $company->email) &nbsp;|&nbsp; @endif
            @if ($company->email) Email: {{ $company->email }} @endif
        </div>
        @if ($company->gst_number)
            <div class="gst">GSTIN: {{ $company->gst_number }}</div>
        @endif
    @endif
</div>

<div class="invoice-title-bar">{{ strtoupper($title) }}</div>

<table class="meta-grid">
    <tr>
        <td style="width: 33%;">
            <span class="label">Invoice Number</span>
            <span class="value">{{ $saleInvoice->invoice_number }}</span>
        </td>
        <td style="width: 33%;">
            <span class="label">Invoice Date</span>
            <span class="value">{{ $saleInvoice->invoice_date->format('d M Y') }}</span>
        </td>
        <td style="width: 34%;">
            <span class="label">Type</span>
            <span class="pill {{ $pillClass }}">{{ $saleInvoice->invoice_type_label }}</span>
        </td>
    </tr>
    <tr>
        <td>
            <span class="label">Generated</span>
            <span class="value" style="font-size: 10px;">{{ $saleInvoice->created_at->format('d M Y H:i') }}</span>
        </td>
        <td>
            <span class="label">Sale Records</span>
            <span class="value">{{ $saleInvoice->sales_count }}</span>
        </td>
        <td>
            <span class="label">Printed On</span>
            <span class="value" style="font-size: 10px;">{{ $generatedAt }}</span>
        </td>
    </tr>
</table>

@if ($saleInvoice->notes)
    <div class="notes-box">
        <span class="notes-label">Notes:</span>{{ $saleInvoice->notes }}
    </div>
@endif

<table class="items">
    <thead>
        <tr>
            <th style="width: 4%;">#</th>
            <th style="width: 7%;">Sale</th>
            <th style="width: 7%;">Time</th>
            <th style="width: 13%;">Seller</th>
            <th style="width: 27%;">Product</th>
            <th style="width: 7%;">Qty</th>
            <th style="width: 11%;">Rate</th>
            <th style="width: 12%;">Line Total</th>
            <th style="width: 12%;">On Invoice</th>
        </tr>
    </thead>
    <tbody>
        @php $n = 1; @endphp
        @forelse ($saleInvoice->sales as $sale)
            @foreach ($sale->items as $item)
                @php
                    $onInvoice = \App\Models\SaleInvoice::lineAmountForInvoiceType($sale, $item, $type);
                @endphp
                <tr>
                    <td class="center">{{ $n++ }}</td>
                    <td class="center">#{{ $sale->id }}</td>
                    <td class="center">{{ $sale->created_at?->format('H:i') ?? '—' }}</td>
                    <td>{{ $sale->seller_name ?? '—' }}</td>
                    <td>{{ $item->product?->product_name ?? '—' }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">₹{{ number_format($item->selling_price, 2) }}</td>
                    <td class="num">₹{{ number_format($item->total_price, 2) }}</td>
                    <td class="num"><strong>₹{{ number_format($onInvoice, 2) }}</strong></td>
                </tr>
            @endforeach
        @empty
            <tr>
                <td colspan="9" class="center" style="padding: 18px; color: #9ca3af;">No line items.</td>
            </tr>
        @endforelse

        <tr class="grand-total">
            <td colspan="8" style="text-align: right;">GRAND TOTAL (THIS INVOICE)</td>
            <td class="num">₹{{ number_format($saleInvoice->total_amount, 2) }}</td>
        </tr>
    </tbody>
</table>

<table class="summary">
    <tr>
        <td>
            <div class="label">Grand Total</div>
            <div class="value">₹{{ number_format($saleInvoice->total_amount, 2) }}</div>
        </td>
        <td>
            <div class="label">Cash</div>
            <div class="value cash">₹{{ number_format($saleInvoice->cash_total, 2) }}</div>
        </td>
        <td>
            <div class="label">Online</div>
            <div class="value online">₹{{ number_format($saleInvoice->online_total, 2) }}</div>
        </td>
        <td>
            <div class="label">Sale Records</div>
            <div class="value">{{ $saleInvoice->sales_count }}</div>
        </td>
    </tr>
</table>

<table class="signature-row">
    <tr>
        <td>Customer Signature</td>
        <td>Prepared By</td>
        <td>For {{ $companyName }}</td>
    </tr>
</table>

<div class="footer">
    This is a computer-generated invoice and does not require a physical signature.
</div>

</body>
</html>
