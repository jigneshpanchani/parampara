@extends('layouts.admin')

@section('title', 'Expenses')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Expenses</h1>
        <a href="{{ route('admin.expenses.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add Expense
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category_id" id="category_id"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm min-w-[180px]">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                <select name="payment_method" id="payment_method"
                    class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm min-w-[160px]">
                    <option value="">All Methods</option>
                    <option value="Cash" {{ request('payment_method') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="G-Pay" {{ request('payment_method') === 'G-Pay' ? 'selected' : '' }}>G-Pay</option>
                    <option value="Online Transfer" {{ request('payment_method') === 'Online Transfer' ? 'selected' : '' }}>Online Transfer</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Filter
                </button>
                <a href="{{ route('admin.expenses.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm inline-flex items-center">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Method</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($expenses as $index => $expense)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $expenses->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $expense->expense_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->expenseCategory?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">₹{{ number_format($expense->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($expense->payment_method == 'Cash')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">💵 Cash</span>
                            @elseif($expense->payment_method == 'G-Pay')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">📱 G-Pay</span>
                            @elseif($expense->payment_method == 'Online Transfer')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">🏦 Online Transfer</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm flex gap-3">
                            <a href="{{ route('admin.expenses.show', $expense) }}" class="text-green-500 hover:text-green-700 transition" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" class="inline delete-form" data-item-name="Expense #{{ $expense->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No expenses found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-0 text-sm text-gray-600 text-left">
            Showing {{ $expenses->total() > 0 ? $expenses->firstItem() . ' to ' . $expenses->lastItem() . ' of' : '0 of' }} {{ $expenses->total() }} results
        </div>
        <div class="flex-1 min-w-0 text-gray-700 font-semibold text-center">
            Total (this page): <span class="text-gray-900">₹{{ number_format($expenses->sum('amount'), 2) }}</span>
            &nbsp;·&nbsp; All total: <span class="text-gray-900">₹{{ number_format($totalAmount ?? 0, 2) }}</span>
        </div>
        <div class="flex-1 min-w-0 flex justify-end">
            {{ $expenses->links('vendor.pagination.tailwind-links-only') }}
        </div>
    </div>
</div>
@endsection

