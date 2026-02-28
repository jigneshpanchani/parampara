@extends('layouts.admin')

@section('title', 'Expense Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Expense #{{ $expense->id }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.expenses.edit', $expense) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Edit</a>
                <a href="{{ route('admin.expenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Back</a>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Expense Date</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ $expense->expense_date->format('d M Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Category</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ $expense->expenseCategory?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Payment Method</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ $expense->payment_method }}</dd>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <dt class="text-sm font-semibold text-gray-700">Amount</dt>
                    <dd class="text-lg font-bold text-gray-900">₹{{ number_format($expense->amount, 2) }}</dd>
                </div>
            </dl>
        </div>

        @if ($expense->description)
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Description</h3>
                <p class="text-sm text-gray-700">{{ $expense->description }}</p>
            </div>
        @endif

        @if ($expense->notes)
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $expense->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
