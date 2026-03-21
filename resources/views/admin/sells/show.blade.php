@extends('layouts.admin')

@section('title', 'Sale Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Sale #{{ $sell->id }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.sells.edit', $sell) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Edit</a>
                <a href="{{ route('admin.sells.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <!-- Sale Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Sale Info</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Sale Date</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $sell->sell_date->format('d M Y') }}</dd>
                    </div>
                    @if ($sell->cashSellInvoice)
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-600">Cash invoice</dt>
                        <dd>
                            <a href="{{ route('admin.sell-invoices.show', $sell->cashSellInvoice) }}" class="text-sm font-mono font-semibold text-blue-600 hover:underline">{{ $sell->cashSellInvoice->invoice_number }}</a>
                        </dd>
                    </div>
                    @endif
                    @if ($sell->onlineSellInvoice)
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-600">Online invoice</dt>
                        <dd>
                            <a href="{{ route('admin.sell-invoices.show', $sell->onlineSellInvoice) }}" class="text-sm font-mono font-semibold text-purple-600 hover:underline">{{ $sell->onlineSellInvoice->invoice_number }}</a>
                        </dd>
                    </div>
                    @endif
                    @if ($sell->mixSellInvoice)
                    <div class="flex justify-between items-center">
                        <dt class="text-sm text-gray-600">Mix invoice</dt>
                        <dd>
                            <a href="{{ route('admin.sell-invoices.show', $sell->mixSellInvoice) }}" class="text-sm font-mono font-semibold text-indigo-600 hover:underline">{{ $sell->mixSellInvoice->invoice_number }}</a>
                        </dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Seller Name</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $sell->seller_name ?? '-' }}</dd>
                    </div>
                    @if ($sell->seller_contact_number)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Contact</dt>
                        <dd class="text-sm text-gray-900">{{ $sell->seller_contact_number }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Payment Status</dt>
                        <dd>
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($sell->payment_status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($sell->payment_status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Payment Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Payment Details</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Payment Mode</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $sell->payment_mode_label }}</dd>
                    </div>
                    @if ($sell->payment_mode === 'mix')
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Cash Amount</dt>
                        <dd class="text-sm text-blue-700 font-semibold">₹{{ number_format($sell->cash_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Online Amount</dt>
                        <dd class="text-sm text-purple-700 font-semibold">₹{{ number_format($sell->online_amount, 2) }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Amount Paid</dt>
                        <dd class="text-sm font-semibold text-green-600">₹{{ number_format($sell->amount_paid, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <dt class="text-sm font-semibold text-gray-700">Total Amount</dt>
                        <dd class="text-sm font-bold text-gray-900">₹{{ number_format($sell->total_amount, 2) }}</dd>
                    </div>
                    @if ($sell->pending_amount > 0)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Pending Amount</dt>
                        <dd class="text-sm font-semibold text-red-600">₹{{ number_format($sell->pending_amount, 2) }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Items Table -->
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Items</h3>
        <div class="overflow-x-auto mb-6">
            <table class="w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold">Product</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold">Qty</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold">Price</th>
                        <th class="px-4 py-2 text-right text-sm font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sell->items as $i => $item)
                        <tr class="border-b">
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $i + 1 }}</td>
                            <td class="px-4 py-2 text-sm">{{ $item->product->product_name ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-right">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-sm text-right">₹{{ number_format($item->selling_price, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right font-semibold">₹{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right font-semibold text-gray-700">Total</td>
                        <td class="px-4 py-2 text-right font-bold text-gray-900">₹{{ number_format($sell->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if ($sell->notes)
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $sell->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
