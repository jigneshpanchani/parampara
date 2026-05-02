@extends('layouts.admin')

@section('title', $saleInvoice->invoice_number)

@section('content')
<div class="mb-6 flex justify-between items-start flex-wrap gap-4">
    <div>
        <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-3xl font-bold text-gray-800">{{ $saleInvoice->invoice_number }}</h2>
            @if($saleInvoice->invoice_type === 'online')
                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">Online payment invoice</span>
            @elseif($saleInvoice->invoice_type === 'mix')
                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">Mix payment invoice</span>
            @else
                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">Cash invoice</span>
            @endif
        </div>
        <p class="text-gray-600 mt-1">Date: <strong>{{ $saleInvoice->invoice_date->format('d M Y') }}</strong></p>
        <p class="text-gray-500 text-sm">Created {{ $saleInvoice->created_at->format('d M Y H:i') }}</p>
        @if ($saleInvoice->notes)
            <p class="text-gray-700 mt-2"><span class="font-semibold">Notes:</span> {{ $saleInvoice->notes }}</p>
        @endif
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.sale-invoices.export', $saleInvoice) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
            📥 Download Excel
        </a>
        <a href="{{ route('admin.sale-invoices.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">All invoices</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Grand total (this invoice)</p>
        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($saleInvoice->total_amount, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Cash</p>
        <p class="text-2xl font-bold text-blue-700">₹{{ number_format($saleInvoice->cash_total, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Online</p>
        <p class="text-2xl font-bold text-purple-700">₹{{ number_format($saleInvoice->online_total, 2) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm font-semibold">Sale records</p>
        <p class="text-2xl font-bold text-gray-800">{{ $saleInvoice->sales_count }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sale #</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Qty</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Line total</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Amount (this invoice)</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Sale</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($saleInvoice->sales as $sale)
                @foreach ($sale->items as $item)
                    @php
                        $onInvoice = \App\Models\SaleInvoice::lineAmountForInvoiceType($sale, $item, $saleInvoice->invoice_type);
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-mono text-gray-700">#{{ $sale->id }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $sale->seller_name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $item->product?->product_name ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-right">{{ $item->quantity }}</td>
                        <td class="px-6 py-3 text-sm text-right">₹{{ number_format($item->total_price, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-right font-semibold text-gray-900">₹{{ number_format($onInvoice, 2) }}</td>
                        <td class="px-6 py-3 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sale->payment_mode === 'cash' ? 'bg-blue-100 text-blue-800' : ($sale->payment_mode === 'mix' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800') }}">
                                {{ $sale->payment_mode_label }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <a href="{{ route('admin.sales.show', $sale) }}" class="text-blue-600 hover:underline text-sm">View sale</a>
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6 flex gap-4">
    <form action="{{ route('admin.sale-invoices.destroy', $saleInvoice) }}" method="POST" onsubmit="return confirm('Remove this invoice from the list? Sales can be invoiced again for this type.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:underline">Remove from list</button>
    </form>
</div>
@endsection
