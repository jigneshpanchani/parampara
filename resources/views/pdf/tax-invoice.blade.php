<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $taxInvoice->invoice_number }}</title>
    <style>
        @page { margin: 10mm 10mm 10mm 10mm; }
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
            margin: 0;
        }
        .frame { border: 1.5px solid #111827; border-top: 5px solid #e23a2e; }
        table { border-collapse: collapse; width: 100%; }
        .pad { padding: 4px 6px; }

        /* Header */
        .header-table td { border: none; vertical-align: middle; }
        .company-name { font-size: 22px; font-weight: bold; color: #e23a2e; }
        .company-sub { font-size: 9px; color: #374151; }
        .title-bar {
            text-align: center; font-size: 13px; font-weight: bold;
            letter-spacing: 2px; padding: 4px;
            background: #e23a2e; color: #fff;
        }

        /* Party grid */
        .party td { border: 1px solid #9ca3af; vertical-align: top; }
        .label { color: #6b7280; font-size: 8px; text-transform: uppercase; }
        .strong { font-weight: bold; }

        /* Items */
        table.items th, table.items td { border: 1px solid #9ca3af; padding: 3px 5px; font-size: 9px; }
        table.items th { background: #f3f4f6; font-weight: bold; text-align: center; }
        td.num { text-align: right; }
        td.center { text-align: center; }

        .totals td { border: 1px solid #9ca3af; padding: 3px 6px; font-size: 9px; }
        .grand td { background: #e23a2e; color: #fff; font-weight: bold; font-size: 12px; }

        .words { font-size: 9px; padding: 4px 6px; }
        .bank { font-size: 9px; }
        .terms { font-size: 8px; color: #374151; }
        .sign { font-size: 9px; text-align: center; }
    </style>
</head>
<body>
@php
    $companyName = $company->company_name ?? 'Parampara';
    $logoSrc = $logoSrc ?? null;
    $qtyFmt = fn ($q) => rtrim(rtrim(number_format((float) $q, 3, '.', ''), '0'), '.');
    $pctFmt = fn ($p) => rtrim(rtrim(number_format((float) $p, 2, '.', ''), '0'), '.');
    $terms = config('tax_invoice.terms', []);
@endphp

<div class="frame">
    {{-- Seller header --}}
    <table class="header-table">
        <tr>
            <td class="pad">
                <div class="company-name">{{ strtoupper($companyName) }}</div>
                @if ($company)
                    @if ($company->address)<div class="company-sub">{{ $company->address }}</div>@endif
                    <div class="company-sub">
                        @if ($company->phone) (M) {{ $company->phone }} @endif
                        @if ($company->email) &nbsp; Email: {{ $company->email }} @endif
                        @if ($company->website_url) &nbsp; {{ $company->website_url }} @endif
                    </div>
                @endif
            </td>
            @if ($logoSrc)
                <td class="pad" style="width: 140px; text-align: right;">
                    <img src="{{ $logoSrc }}" alt="{{ $companyName }}" style="max-height: 70px; max-width: 140px;">
                </td>
            @endif
        </tr>
    </table>

    <div class="title-bar">TAX INVOICE</div>

    {{-- Seller GSTIN / FSSAI + Invoice meta --}}
    <table class="party">
        <tr>
            <td class="pad" style="width: 55%;">
                <span class="label">GSTIN</span><br>
                <span class="strong">{{ $company->gst_number ?? '—' }}</span>
                @if ($company && $company->state_code)
                    <br><span class="label">State</span> <span class="strong">{{ $company->state_code }}</span>
                @endif
                @if ($company && $company->fssai_number)
                    <br><span class="label">Food Lic. No</span> {{ $company->fssai_number }}
                @endif
            </td>
            <td class="pad" style="width: 45%;">
                <table>
                    <tr>
                        <td><span class="label">Invoice No</span><br><span class="strong">{{ $taxInvoice->invoice_number }}</span></td>
                        <td><span class="label">Date</span><br><span class="strong">{{ $taxInvoice->invoice_date->format('d/m/Y') }}</span></td>
                    </tr>
                    @if ($taxInvoice->vehicle_no || $taxInvoice->transport)
                    <tr>
                        <td><span class="label">Vehicle No</span><br>{{ $taxInvoice->vehicle_no ?: '—' }}</td>
                        <td><span class="label">Transport</span><br>{{ $taxInvoice->transport ?: '—' }}</td>
                    </tr>
                    @endif
                    @if ($taxInvoice->broker || $taxInvoice->eway_bill_no)
                    <tr>
                        <td><span class="label">Broker</span><br>{{ $taxInvoice->broker ?: '—' }}</td>
                        <td><span class="label">E-Way Bill</span><br>{{ $taxInvoice->eway_bill_no ?: '—' }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
        {{-- Buyer --}}
        <tr>
            <td class="pad" colspan="2">
                <span class="label">M/s.</span>
                <span class="strong">{{ $taxInvoice->buyer_name }}</span>
                @if ($taxInvoice->buyer_address)<br>{{ $taxInvoice->buyer_address }}@endif
                <br>
                @if ($taxInvoice->buyer_gstin)<span class="label">GSTIN</span> {{ $taxInvoice->buyer_gstin }} &nbsp;&nbsp;@endif
                @if ($taxInvoice->buyer_state)<span class="label">Place of Supply</span> {{ $taxInvoice->buyer_state }}@endif
                @if ($taxInvoice->buyer_contact_number)&nbsp;&nbsp;<span class="label">Contact</span> {{ $taxInvoice->buyer_contact_number }}@endif
            </td>
        </tr>
    </table>

    {{-- Line items --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 4%;">Sr</th>
                <th style="width: 34%;">Product Name</th>
                <th style="width: 11%;">HSN/SAC</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 8%;">Unit</th>
                <th style="width: 12%;">Rate</th>
                <th style="width: 7%;">GST %</th>
                <th style="width: 14%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($taxInvoice->items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="center">{{ $item->hsn_code ?: '—' }}</td>
                    <td class="num">{{ $qtyFmt($item->quantity) }}</td>
                    <td class="center">{{ $item->unit_of_measure ?: '—' }}</td>
                    <td class="num">{{ number_format($item->rate, 2) }}</td>
                    <td class="num">{{ $pctFmt($item->gst_rate) }}</td>
                    <td class="num">{{ number_format($item->taxable_amount, 2) }}</td>
                </tr>
            @endforeach
            {{-- Filler rows to keep the box tidy when there are few items --}}
            @for ($k = count($taxInvoice->items); $k < 6; $k++)
                <tr>
                    <td class="center">&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals">
        <tr>
            <td style="width: 60%;" rowspan="{{ $taxInvoice->is_interstate ? 3 : 4 }}" class="words">
                <span class="label">Amount in words</span><br>
                <span class="strong">{{ $taxInvoice->amount_in_words }}</span>
            </td>
            <td style="width: 25%;" class="num">Taxable Amount</td>
            <td style="width: 15%;" class="num">{{ number_format($taxInvoice->taxable_amount, 2) }}</td>
        </tr>
        @if ($taxInvoice->is_interstate)
            <tr>
                <td class="num">IGST</td>
                <td class="num">{{ number_format($taxInvoice->igst_amount, 2) }}</td>
            </tr>
        @else
            <tr>
                <td class="num">Central Tax (CGST)</td>
                <td class="num">{{ number_format($taxInvoice->cgst_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="num">State/UT Tax (SGST)</td>
                <td class="num">{{ number_format($taxInvoice->sgst_amount, 2) }}</td>
            </tr>
        @endif
        <tr class="grand">
            <td class="num">Grand Total</td>
            <td class="num">{{ number_format($taxInvoice->grand_total, 2) }}</td>
        </tr>
    </table>

    {{-- Bank + terms + signature --}}
    <table>
        <tr>
            <td class="pad bank" style="width: 50%; border-right: 1px solid #9ca3af; vertical-align: top;">
                @if ($company && ($company->bank_name || $company->bank_account_number))
                    <span class="label">Bank Details</span><br>
                    @if ($company->bank_name)Bank Name : {{ $company->bank_name }}<br>@endif
                    @if ($company->bank_account_number)A/c No. : {{ $company->bank_account_number }}<br>@endif
                    @if ($company->bank_ifsc)IFSC : {{ $company->bank_ifsc }}@endif
                @endif
            </td>
            <td class="pad sign" style="width: 50%; vertical-align: top;">
                For, {{ strtoupper($companyName) }}
                <div style="height: 40px;"></div>
                (Authorised Signatory)
            </td>
        </tr>
    </table>

    @if (! empty($terms))
        <div class="pad terms" style="border-top: 1px solid #9ca3af;">
            <span class="label">Terms &amp; Conditions</span>
            @foreach ($terms as $i => $term)
                {{ $i + 1 }}. {{ $term }}
            @endforeach
        </div>
    @endif
</div>
</body>
</html>
