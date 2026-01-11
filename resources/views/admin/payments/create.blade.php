@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">💳 Create Payment</h1>
        <p class="text-gray-600 mt-2">Record a new payment for a purchase</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-8 max-w-2xl">
        <form action="{{ route('admin.payments.store') }}" method="POST">
            @csrf

            <!-- Purchase Selection -->
            <div class="mb-6">
                <label for="purchase_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    Purchase <span class="text-red-600">*</span>
                </label>
                <select name="purchase_id" id="purchase_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('purchase_id') border-red-500 @enderror" required>
                    <option value="">-- Select Purchase --</option>
                    @foreach($purchases as $purchase)
                    <option value="{{ $purchase->id }}" @selected(old('purchase_id') == $purchase->id)>
                        Purchase #{{ $purchase->id }} - ₹{{ number_format($purchase->total_amount, 2) }} ({{ $purchase->supplier_name }})
                    </option>
                    @endforeach
                </select>
                @error('purchase_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Date -->
            <div class="mb-6">
                <label for="payment_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    Payment Date <span class="text-red-600">*</span>
                </label>
                <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('payment_date') border-red-500 @enderror" required>
                @error('payment_date')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                    Amount <span class="text-red-600">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" value="{{ old('amount') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror" placeholder="0.00" required>
                @error('amount')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">
                    Payment Method <span class="text-red-600">*</span>
                </label>
                <select name="payment_method" id="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('payment_method') border-red-500 @enderror" required>
                    <option value="">-- Select Method --</option>
                    <option value="cash" @selected(old('payment_method') == 'cash')>Cash</option>
                    <option value="cheque" @selected(old('payment_method') == 'cheque')>Cheque</option>
                    <option value="bank_transfer" @selected(old('payment_method') == 'bank_transfer')>Bank Transfer</option>
                    <option value="credit_card" @selected(old('payment_method') == 'credit_card')>Credit Card</option>
                    <option value="other" @selected(old('payment_method') == 'other')>Other</option>
                </select>
                @error('payment_method')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Status -->
            <div class="mb-6">
                <label for="payment_status" class="block text-sm font-semibold text-gray-700 mb-2">
                    Payment Status <span class="text-red-600">*</span>
                </label>
                <select name="payment_status" id="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('payment_status') border-red-500 @enderror" required>
                    <option value="">-- Select Status --</option>
                    <option value="pending" @selected(old('payment_status') == 'pending')>Pending</option>
                    <option value="paid" @selected(old('payment_status') == 'paid')>Paid</option>
                    <option value="failed" @selected(old('payment_status') == 'failed')>Failed</option>
                    <option value="cancelled" @selected(old('payment_status') == 'cancelled')>Cancelled</option>
                </select>
                @error('payment_status')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reference Number -->
            <div class="mb-6">
                <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-2">
                    Reference Number
                </label>
                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., CHQ123456">
                @error('reference_number')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                    Notes
                </label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                    Create Payment
                </button>
                <a href="{{ route('admin.payments.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

