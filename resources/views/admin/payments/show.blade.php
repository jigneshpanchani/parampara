@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">💳 Payment Details</h1>
            <p class="text-gray-600 mt-2">Payment #{{ $payment->id }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.payments.edit', $payment) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                Edit
            </a>
            <a href="{{ route('admin.payments.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg transition">
                Back
            </a>
        </div>
    </div>

    <!-- Payment Details Card -->
    <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
        <!-- Status Badge -->
        <div class="mb-6">
            <span class="px-4 py-2 rounded-full text-sm font-bold
                @if($payment->payment_status === 'paid') bg-green-100 text-green-800
                @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($payment->payment_status === 'failed') bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($payment->payment_status) }}
            </span>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <!-- Purchase ID -->
            <div>
                <p class="text-sm text-gray-600 mb-1">Purchase ID</p>
                <p class="text-lg font-bold text-gray-800">#{{ $payment->purchase_id }}</p>
            </div>

            <!-- Payment Date -->
            <div>
                <p class="text-sm text-gray-600 mb-1">Payment Date</p>
                <p class="text-lg font-bold text-gray-800">{{ $payment->payment_date->format('d M Y') }}</p>
            </div>

            <!-- Amount -->
            <div>
                <p class="text-sm text-gray-600 mb-1">Amount</p>
                <p class="text-lg font-bold text-green-600">₹{{ number_format($payment->amount, 2) }}</p>
            </div>

            <!-- Payment Method -->
            <div>
                <p class="text-sm text-gray-600 mb-1">Payment Method</p>
                <p class="text-lg font-bold text-gray-800">{{ $payment->getPaymentMethodLabel() }}</p>
            </div>

            <!-- Reference Number -->
            <div>
                <p class="text-sm text-gray-600 mb-1">Reference Number</p>
                <p class="text-lg font-bold text-gray-800">{{ $payment->reference_number ?? 'N/A' }}</p>
            </div>

            <!-- Created Date -->
            <div>
                <p class="text-sm text-gray-600 mb-1">Created Date</p>
                <p class="text-lg font-bold text-gray-800">{{ $payment->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        <!-- Notes -->
        @if($payment->notes)
        <div class="mb-8 pb-8 border-b">
            <p class="text-sm text-gray-600 mb-2">Notes</p>
            <p class="text-gray-800">{{ $payment->notes }}</p>
        </div>
        @endif

        <!-- Purchase Information -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Purchase Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Supplier</p>
                    <p class="font-semibold text-gray-800">{{ $payment->purchase->supplier_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Purchase Date</p>
                    <p class="font-semibold text-gray-800">{{ $payment->purchase->purchase_date->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="font-semibold text-gray-800">₹{{ number_format($payment->purchase->total_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-semibold text-gray-800">{{ ucfirst($payment->purchase->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4">
            @if($payment->payment_status !== 'paid')
            <form action="{{ route('admin.payments.mark-as-paid', $payment) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    Mark as Paid
                </button>
            </form>
            @endif
            <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

