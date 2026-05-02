@extends('layouts.admin')

@section('title', 'Sales Invoices')

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">🧾 Sales Invoices</h2>
        <p class="text-gray-600 text-sm mt-1">Three types per day: <strong>Cash</strong>, <strong>Online</strong> (UPI / G-Pay), <strong>Mix</strong> (combined cash + online on one sale).</p>
    </div>
    <a href="{{ route('admin.sale-invoices.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        + Generate Invoice
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-100 border-b">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Invoice #</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Type</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Sales</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Total</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Cash</th>
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Online</th>
                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($invoices as $inv)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-mono font-semibold text-gray-900">{{ $inv->invoice_number }}</td>
                    <td class="px-6 py-4 text-sm">
                        @if($inv->invoice_type === 'online')
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-purple-100 text-purple-800">Online</span>
                        @elseif($inv->invoice_type === 'mix')
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-indigo-100 text-indigo-800">Mix</span>
                        @else
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">Cash</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $inv->invoice_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $inv->sales_count }}</td>
                    <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">₹{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-blue-700">₹{{ number_format($inv->cash_total, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-purple-700">₹{{ number_format($inv->online_total, 2) }}</td>
                    <td class="px-6 py-4 text-sm">
                        @php $firstSaleId = $firstSaleByInvoice[$inv->id] ?? null; @endphp
                        <div class="flex justify-center items-center gap-3">
                            @if ($firstSaleId)
                                <a href="{{ route('admin.sales.show', $firstSaleId) }}"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="View sale">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                            @else
                                <span class="text-gray-300" title="No linked sale">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </span>
                            @endif

                            <a href="{{ route('admin.sale-invoices.export', $inv) }}"
                                class="text-green-600 hover:text-green-800"
                                title="Download Excel">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>

                            <form action="{{ route('admin.sale-invoices.destroy', $inv) }}" method="POST" class="inline-flex" onsubmit="return confirm('Remove this invoice from the list? Sales can be invoiced again for this type. The record is kept internally.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-800"
                                    title="Remove">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-600">No invoices yet. <a href="{{ route('admin.sale-invoices.create') }}" class="text-blue-600 hover:underline">Generate one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($invoices->hasPages())
    <div class="mt-4">{{ $invoices->links() }}</div>
@endif

<div class="mt-6">
    <a href="{{ route('admin.sales.index') }}" class="text-blue-500 hover:underline">← Back to Sales</a>
</div>
@endsection
