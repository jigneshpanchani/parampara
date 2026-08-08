@extends('layouts.admin')

@section('title', $taxInvoice->invoice_number)

@php
    $companyName = $company->company_name ?? 'Parampara';
@endphp

@section('content')
<div class="mb-6 flex justify-between items-start flex-wrap gap-4">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">{{ $taxInvoice->invoice_number }}</h2>
        <p class="text-gray-600 mt-1">Date: <strong>{{ $taxInvoice->invoice_date->format('d M Y') }}</strong></p>
        <p class="text-gray-500 text-sm">Buyer: {{ $taxInvoice->buyer_name }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.tax-invoices.pdf', $taxInvoice) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">📄 Download PDF</a>
        <a href="{{ route('admin.tax-invoices.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">All invoices</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Taxable</p>
        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($taxInvoice->taxable_amount, 2) }}</p>
    </div>
    @if ($taxInvoice->is_interstate)
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-semibold">IGST</p>
            <p class="text-2xl font-bold text-purple-700">₹{{ number_format($taxInvoice->igst_amount, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-semibold">Tax type</p>
            <p class="text-lg font-bold text-gray-800">Inter-state</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-semibold">CGST</p>
            <p class="text-2xl font-bold text-blue-700">₹{{ number_format($taxInvoice->cgst_amount, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm font-semibold">SGST</p>
            <p class="text-2xl font-bold text-blue-700">₹{{ number_format($taxInvoice->sgst_amount, 2) }}</p>
        </div>
    @endif
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Grand Total</p>
        <p class="text-2xl font-bold text-green-700">₹{{ number_format($taxInvoice->grand_total, 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
        <div>
            <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs mb-2">Buyer</h3>
            <p class="font-semibold text-gray-800">{{ $taxInvoice->buyer_name }}</p>
            @if ($taxInvoice->buyer_address)<p class="text-gray-600 whitespace-pre-line">{{ $taxInvoice->buyer_address }}</p>@endif
            @if ($taxInvoice->buyer_gstin)<p class="text-gray-600">GSTIN: {{ $taxInvoice->buyer_gstin }}</p>@endif
            @if ($taxInvoice->buyer_state)<p class="text-gray-600">Place of Supply: {{ $taxInvoice->buyer_state }}</p>@endif
            @if ($taxInvoice->buyer_contact_number)<p class="text-gray-600">Contact: {{ $taxInvoice->buyer_contact_number }}</p>@endif
        </div>
        <div>
            <h3 class="font-bold text-gray-700 uppercase tracking-wide text-xs mb-2">Transport / Reference</h3>
            @if ($taxInvoice->vehicle_no)<p class="text-gray-600">Vehicle: {{ $taxInvoice->vehicle_no }}</p>@endif
            @if ($taxInvoice->transport)<p class="text-gray-600">Transport: {{ $taxInvoice->transport }}</p>@endif
            @if ($taxInvoice->broker)<p class="text-gray-600">Broker: {{ $taxInvoice->broker }}</p>@endif
            @if ($taxInvoice->eway_bill_no)<p class="text-gray-600">E-Way Bill: {{ $taxInvoice->eway_bill_no }}</p>@endif
            @if ($taxInvoice->notes)<p class="text-gray-600 mt-2"><span class="font-semibold">Notes:</span> {{ $taxInvoice->notes }}</p>@endif
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Product</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">HSN/SAC</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Qty</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Unit</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Rate</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">GST %</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-700">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taxInvoice->items as $index => $item)
                    <tr class="border-b">
                        <td class="px-4 py-3 text-gray-700">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-gray-800">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->hsn_code ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ rtrim(rtrim(number_format($item->quantity, 3), '0'), '.') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $item->unit_of_measure ?? '—' }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">₹{{ number_format($item->rate, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-700">{{ rtrim(rtrim(number_format($item->gst_rate, 2), '0'), '.') }}%</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">₹{{ number_format($item->taxable_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t bg-gray-50">
                    <td colspan="7" class="px-4 py-2 text-right font-semibold text-gray-700">Taxable Amount</td>
                    <td class="px-4 py-2 text-right font-semibold text-gray-900">₹{{ number_format($taxInvoice->taxable_amount, 2) }}</td>
                </tr>
                @if ($taxInvoice->is_interstate)
                    <tr class="bg-gray-50">
                        <td colspan="7" class="px-4 py-2 text-right text-gray-700">IGST</td>
                        <td class="px-4 py-2 text-right text-gray-900">₹{{ number_format($taxInvoice->igst_amount, 2) }}</td>
                    </tr>
                @else
                    <tr class="bg-gray-50">
                        <td colspan="7" class="px-4 py-2 text-right text-gray-700">CGST</td>
                        <td class="px-4 py-2 text-right text-gray-900">₹{{ number_format($taxInvoice->cgst_amount, 2) }}</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td colspan="7" class="px-4 py-2 text-right text-gray-700">SGST</td>
                        <td class="px-4 py-2 text-right text-gray-900">₹{{ number_format($taxInvoice->sgst_amount, 2) }}</td>
                    </tr>
                @endif
                <tr class="bg-gray-800 text-white">
                    <td colspan="7" class="px-4 py-3 text-right font-bold">GRAND TOTAL</td>
                    <td class="px-4 py-3 text-right font-bold">₹{{ number_format($taxInvoice->grand_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="px-4 py-3 text-sm text-gray-600 border-t">
        <span class="font-semibold">Amount in words:</span> {{ $taxInvoice->amount_in_words }}
    </div>
</div>

<div class="mt-6">
    <form action="{{ route('admin.tax-invoices.destroy', $taxInvoice) }}" method="POST" onsubmit="return confirm('Remove this tax invoice?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:underline">Delete invoice</button>
    </form>
</div>
@endsection
