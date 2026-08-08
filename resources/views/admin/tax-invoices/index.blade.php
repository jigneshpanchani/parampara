@extends('layouts.admin')

@section('title', 'Tax Invoices')

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Tax Invoices</h2>
        <p class="text-gray-600 text-sm mt-1">Formal GST invoices for a single customer (HSN, CGST/SGST).</p>
    </div>
    <a href="{{ route('admin.tax-invoices.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">+ New Tax Invoice</a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Invoice #</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Buyer</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Items</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Taxable</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Grand Total</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm font-mono font-semibold text-gray-800">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-3 text-sm text-gray-700">{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td class="px-6 py-3 text-sm text-gray-800">{{ $invoice->buyer_name }}</td>
                        <td class="px-6 py-3 text-sm text-right text-gray-700">{{ $invoice->items_count }}</td>
                        <td class="px-6 py-3 text-sm text-right text-gray-700">₹{{ number_format($invoice->taxable_amount, 2) }}</td>
                        <td class="px-6 py-3 text-sm text-right font-bold text-gray-900">₹{{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="px-6 py-3 text-center whitespace-nowrap">
                            <a href="{{ route('admin.tax-invoices.show', $invoice) }}" class="text-blue-600 hover:underline text-sm">View</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('admin.tax-invoices.pdf', $invoice) }}" class="text-red-600 hover:underline text-sm">PDF</a>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('admin.tax-invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('Remove tax invoice {{ $invoice->invoice_number }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                            No tax invoices yet.
                            <a href="{{ route('admin.tax-invoices.create') }}" class="text-blue-600 hover:underline">Create the first one</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $invoices->links() }}
</div>
@endsection
