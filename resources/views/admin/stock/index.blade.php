@extends('layouts.admin')

@section('title', 'Stock')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-bold text-gray-800">📦 Stock</h2>
    <p class="text-gray-600 text-sm mt-1">Inventory snapshot, monthly closing entries, and verification tools.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    {{-- Current Stock --}}
    <a href="{{ route('admin.stocks.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-blue-500">
        <div class="text-4xl mb-4">📦</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Current Stock</h3>
        <p class="text-gray-600 text-sm">Live inventory across all products with quantity on hand.</p>
    </a>

    {{-- Save Closing Stock (history & save) --}}
    <a href="{{ route('admin.stock-closings.history') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-purple-500">
        <div class="text-4xl mb-4">🗂️</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Save Closing Stock</h3>
        <p class="text-gray-600 text-sm">Record month-end closing qty per product and view saved history.</p>
    </a>

    {{-- Stock Closing Verification --}}
    <a href="{{ route('admin.stock-closings.index') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition border-l-4 border-green-500">
        <div class="text-4xl mb-4">✅</div>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Stock Closing Verification</h3>
        <p class="text-gray-600 text-sm">Pick a date range, cross-check opening + purchase − sell + returns vs actual closing.</p>
    </a>

</div>
@endsection
