@extends('layouts.admin')

@section('title', 'Stock Closing — ' . $stockClosing->closing_date->format('d M Y'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Closing — {{ $stockClosing->closing_date->format('d M Y') }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($stockClosing->closing_date)->format('F Y') }}</p>
            </div>
            <a href="{{ route('admin.stock-closings.history') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">Back</a>
        </div>

        @if($stockClosing->notes)
        <div class="mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
            <strong>Notes:</strong> {{ $stockClosing->notes }}
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Product</th>
                        <th class="px-4 py-2 text-right font-semibold text-purple-700">Closing Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockClosing->items->sortBy(fn($i) => $i->product->product_name ?? '') as $item)
                    <tr class="border-b">
                        <td class="px-4 py-2 font-medium text-gray-800">{{ $item->product->product_name ?? '—' }}</td>
                        <td class="px-4 py-2 text-right font-semibold text-purple-700">{{ number_format($item->actual_stock, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
