@extends('layouts.admin')

@section('title', 'Sell Invoices')

@section('content')
@if (session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
@endif

<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">🧾 Sell Invoices</h2>
        <p class="text-gray-600 text-sm mt-1">Three types per day: <strong>Cash</strong>, <strong>Online</strong> (UPI / G-Pay), <strong>Mix</strong> (combined cash + online on one sale).</p>
    </div>
    <a href="{{ route('admin.sell-invoices.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
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
                    <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $inv->sells_count }}</td>
                    <td class="px-6 py-4 text-sm text-right font-semibold text-gray-900">₹{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-blue-700">₹{{ number_format($inv->cash_total, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right text-purple-700">₹{{ number_format($inv->online_total, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-center">
                        @php $firstSellId = $firstSellByInvoice[$inv->id] ?? null; @endphp
                        @if ($firstSellId)
                            <a href="{{ route('admin.sells.show', $firstSellId) }}" class="text-blue-600 hover:underline mr-3">View sell</a>
                        @else
                            <span class="text-gray-400 mr-3" title="No linked sale">—</span>
                        @endif
                        <a href="{{ route('admin.sell-invoices.export', $inv) }}" class="text-green-600 hover:underline mr-3">Excel</a>
                        <form action="{{ route('admin.sell-invoices.destroy', $inv) }}" method="POST" class="inline" onsubmit="return confirm('Remove this invoice from the list? Sales can be invoiced again for this type. The record is kept internally.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center text-gray-600">No invoices yet. <a href="{{ route('admin.sell-invoices.create') }}" class="text-blue-600 hover:underline">Generate one</a>.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($invoices->hasPages())
    <div class="mt-4">{{ $invoices->links() }}</div>
@endif

<div class="mt-6">
    <a href="{{ route('admin.sells.index') }}" class="text-blue-500 hover:underline">← Back to Sales</a>
</div>
@endsection
