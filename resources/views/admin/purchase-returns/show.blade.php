@extends('layouts.admin')

@section('title', 'Purchase Return Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Purchase Return #{{ $purchaseReturn->id }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.purchase-returns.edit', $purchaseReturn) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Edit</a>
                <a href="{{ route('admin.purchase-returns.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Return Details</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Return Date</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $purchaseReturn->return_date->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Quantity</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $purchaseReturn->quantity }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Return Price</dt>
                        <dd class="text-sm font-semibold text-gray-900">₹{{ number_format($purchaseReturn->return_price, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                        <dt class="text-sm font-semibold text-gray-700">Total Return Amount</dt>
                        <dd class="text-sm font-bold text-green-600">₹{{ number_format($purchaseReturn->total_return_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Related Info</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Purchase</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            @if ($purchaseReturn->purchase)
                                <a href="{{ route('admin.purchases.show', $purchaseReturn->purchase) }}" class="text-blue-500 hover:underline">{{ $purchaseReturn->purchase->supplier_name }} - {{ $purchaseReturn->purchase->purchase_date->format('d M Y') }}</a>
                            @else
                                Purchase #{{ $purchaseReturn->purchase_id }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Product</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $purchaseReturn->product->product_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Reason</dt>
                        <dd class="text-sm text-gray-900">{{ $purchaseReturn->reason ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if ($purchaseReturn->notes)
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $purchaseReturn->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
