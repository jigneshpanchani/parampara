@extends('layouts.admin')

@section('title', 'Generate Sell Invoice')

@section('content')
@if (session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ session('error') }}</div>
@endif

<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">Generate daily sell invoice</h2>
    <p class="text-gray-600 text-sm mt-2">Pick <strong>invoice type</strong> and <strong>date</strong>. You can create <strong>one cash invoice</strong> and <strong>one online invoice</strong> per day.</p>
    <ul class="text-gray-600 text-sm mt-2 list-disc ml-6 space-y-1">
        <li><strong>Cash invoice</strong> — includes cash sales and the <em>cash part</em> of mix payments.</li>
        <li><strong>Online invoice</strong> — UPI / G-Pay sales and the <em>online part</em> of mix payments.</li>
    </ul>
</div>

<div class="bg-white rounded-lg shadow p-8 max-w-2xl">
    <form method="GET" action="{{ route('admin.sell-invoices.create') }}" class="mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label for="preview_date" class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
            <input type="date" name="date" id="preview_date" value="{{ $date }}" class="px-4 py-2 border border-gray-300 rounded-lg">
        </div>
        <div>
            <label for="invoice_type_preview" class="block text-sm font-semibold text-gray-700 mb-2">Invoice type</label>
            <select name="invoice_type" id="invoice_type_preview" class="px-4 py-2 border border-gray-300 rounded-lg min-w-[200px]">
                <option value="cash" {{ $invoiceType === 'cash' ? 'selected' : '' }}>Cash invoice</option>
                <option value="online" {{ $invoiceType === 'online' ? 'selected' : '' }}>Online payment invoice</option>
            </select>
        </div>
        <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">Refresh</button>
    </form>

    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div class="p-3 rounded border border-blue-200 bg-blue-50">
            <span class="font-semibold text-blue-900">Cash — pending</span>
            <span class="block text-2xl font-bold text-blue-800">{{ $pendingCash }}</span>
            @if($alreadyCash)<span class="text-xs text-red-700">Invoice already exists for this date</span>@endif
        </div>
        <div class="p-3 rounded border border-purple-200 bg-purple-50">
            <span class="font-semibold text-purple-900">Online — pending</span>
            <span class="block text-2xl font-bold text-purple-800">{{ $pendingOnline }}</span>
            @if($alreadyOnline)<span class="text-xs text-red-700">Invoice already exists for this date</span>@endif
        </div>
    </div>

    <div class="mb-6 p-4 rounded-lg {{ $alreadyInvoiced ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
        @if ($alreadyInvoiced)
            <p class="text-red-800 font-semibold">A <strong>{{ $invoiceType === 'online' ? 'online' : 'cash' }}</strong> invoice already exists for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}.</p>
            <p class="text-red-700 text-sm mt-1">Delete it from the list first to regenerate.</p>
        @else
            <p class="text-green-900"><span class="font-bold">{{ $pendingCount }}</span> sale row(s) can be included in this <strong>{{ $invoiceType === 'online' ? 'online' : 'cash' }}</strong> invoice.</p>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.sell-invoices.store') }}">
        @csrf
        <input type="hidden" name="invoice_date" value="{{ $date }}">
        <input type="hidden" name="invoice_type" value="{{ $invoiceType }}">

        <div class="mb-6">
            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes (optional)</label>
            <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Reference, shift, etc.">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded disabled:opacity-50" {{ $alreadyInvoiced || $pendingCount === 0 ? 'disabled' : '' }}>
                Generate {{ $invoiceType === 'online' ? 'online' : 'cash' }} invoice
            </button>
            <a href="{{ route('admin.sell-invoices.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded inline-flex items-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
