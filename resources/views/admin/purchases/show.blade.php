@extends('layouts.admin')

@section('title', 'Purchase Details')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Purchase Details</h2>
        <div class="flex gap-3">
            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">✏️ Edit</a>
            <a href="{{ route('admin.purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">← Back</a>
        </div>
    </div>

    {{-- Purchase Header --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Purchase Date</p>
                <p class="text-gray-900 font-medium">{{ $purchase->purchase_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Supplier</p>
                <p class="text-gray-900 font-medium">{{ $purchase->supplier_name }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Bill Type</p>
                @if ($purchase->bill_type === 'gst')
                    <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">GST</span>
                @else
                    <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">Without GST</span>
                @endif
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Due Date</p>
                <p class="text-gray-900 font-medium">{{ $purchase->bill_due_date ? $purchase->bill_due_date->format('d M Y') : '-' }}</p>
            </div>
        </div>

        @if ($purchase->bill_details)
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-500 uppercase font-semibold">Bill Details</p>
                <p class="text-gray-700 text-sm mt-1">{{ $purchase->bill_details }}</p>
            </div>
        @endif
    </div>

    {{-- Purchase Items --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchase Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Product</th>
                        <th class="px-4 py-2 text-right">Qty</th>
                        <th class="px-4 py-2 text-right">Purchase Price</th>
                        <th class="px-4 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $i => $item)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-4 py-2 font-medium text-gray-900">{{ $item->product->product_name ?? '-' }}</td>
                            <td class="px-4 py-2 text-right text-gray-700">{{ $item->quantity }}</td>
                            <td class="px-4 py-2 text-right text-gray-700">₹{{ number_format($item->purchase_price, 2) }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-gray-900">₹{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Cost Summary --}}
        <div class="mt-4 flex justify-end">
            <div class="w-64 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">₹{{ number_format($purchase->getSubtotal(), 2) }}</span>
                </div>
                @if ($purchase->transportation_cost)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transportation:</span>
                        <span class="font-medium">₹{{ number_format($purchase->transportation_cost, 2) }}</span>
                    </div>
                @endif
                @if ($purchase->expense)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expense:</span>
                        <span class="font-medium">₹{{ number_format($purchase->expense, 2) }}</span>
                    </div>
                    @if ($purchase->expense_details)
                        <p class="text-xs text-gray-400">{{ $purchase->expense_details }}</p>
                    @endif
                @endif
                <div class="flex justify-between border-t pt-2 font-bold text-base">
                    <span>Total Amount:</span>
                    <span class="text-blue-600">₹{{ number_format($purchase->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Summary --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Summary</h3>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Payable</p>
                <p class="text-xl font-bold text-blue-600 mt-1">₹{{ number_format($purchase->getTotalPayableAmount(), 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold">Total Paid</p>
                <p class="text-xl font-bold text-green-600 mt-1">₹{{ number_format($purchase->getTotalPaidAmount(), 2) }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <p class="text-xs text-gray-500 uppercase font-semibold">Remaining</p>
                <p class="text-xl font-bold text-yellow-600 mt-1">₹{{ number_format($purchase->getRemainingAmount(), 2) }}</p>
            </div>
        </div>

        @if ($purchase->payments->isNotEmpty())
            <h4 class="text-sm font-semibold text-gray-700 mb-3">Payment History</h4>
            <div class="space-y-2">
                @foreach ($purchase->payments as $payment)
                    <div class="flex justify-between items-center bg-gray-50 rounded px-4 py-2 text-sm border-l-4 border-green-400">
                        <div>
                            <span class="font-semibold text-gray-800">₹{{ number_format($payment->amount, 2) }}</span>
                            <span class="text-gray-500 ml-2">{{ $payment->getPaymentMethodLabel() }}</span>
                            @if ($payment->reference_number)
                                <span class="text-gray-400 ml-2">· Ref: {{ $payment->reference_number }}</span>
                            @endif
                            @if ($payment->notes)
                                <span class="text-gray-400 ml-2">· {{ $payment->notes }}</span>
                            @endif
                        </div>
                        <span class="text-gray-400">{{ $payment->payment_date->format('d M Y') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-400 text-sm text-center py-4">No payments recorded yet.</p>
        @endif
    </div>

</div>
@endsection
