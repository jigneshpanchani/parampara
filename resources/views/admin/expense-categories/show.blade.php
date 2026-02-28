@extends('layouts.admin')

@section('title', 'Expense Category Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $expenseCategory->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('admin.expense-categories.edit', $expenseCategory) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Edit</a>
                <a href="{{ route('admin.expense-categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Back</a>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Category Name</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ $expenseCategory->name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">Total Expenses</dt>
                    <dd class="text-sm font-semibold text-blue-600">{{ $expenseCategory->expenses->count() }}</dd>
                </div>
                @if ($expenseCategory->description)
                <div class="flex flex-col gap-1 border-t pt-2">
                    <dt class="text-sm text-gray-600">Description</dt>
                    <dd class="text-sm text-gray-900">{{ $expenseCategory->description }}</dd>
                </div>
                @endif
            </dl>
        </div>

        @if ($expenseCategory->expenses->count() > 0)
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Expenses</h3>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold">#</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Date</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Description</th>
                            <th class="px-4 py-2 text-right text-sm font-semibold">Amount</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold">Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenseCategory->expenses as $i => $expense)
                            <tr class="border-b">
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-2 text-sm">{{ $expense->expense_date->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-sm">{{ $expense->description ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold">₹{{ number_format($expense->amount, 2) }}</td>
                                <td class="px-4 py-2 text-sm">{{ $expense->payment_method }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-right font-semibold text-gray-700">Total</td>
                            <td class="px-4 py-2 text-right font-bold text-gray-900">₹{{ number_format($expenseCategory->expenses->sum('amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
