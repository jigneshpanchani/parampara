@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Product Details</h2>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.edit', $product) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                ✏️ Edit
            </a>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Photo --}}
            <div class="flex flex-col items-center justify-start">
                @if ($product->photo)
                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->product_name }}"
                         class="w-40 h-40 rounded-lg object-cover shadow">
                @else
                    <div class="w-40 h-40 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-sm">
                        No Photo
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="md:col-span-2 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Product Name</p>
                        <p class="text-gray-900 font-medium">{{ $product->product_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Product Code</p>
                        <p class="text-gray-900 font-medium">{{ $product->product_code }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Base Price Min</p>
                        <p class="text-gray-900 font-medium">₹{{ number_format($product->base_price_min, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Base Price Max</p>
                        <p class="text-gray-900 font-medium">₹{{ number_format($product->base_price_max, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Selling Price</p>
                        <p class="text-gray-900 font-medium">₹{{ number_format($product->sell_price, 2) }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Stock</p>
                    @php $status = $product->getStockStatus(); @endphp
                    <span class="px-2 py-1 text-xs font-medium rounded-full
                        @if($status === 'in_stock') bg-green-100 text-green-800
                        @elseif($status === 'low_stock') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $product->getCurrentStock() }} units
                        ({{ str_replace('_', ' ', ucfirst($status)) }})
                    </span>
                </div>

                @if ($product->description)
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Description</p>
                        <p class="text-gray-700">{{ $product->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 text-xs text-gray-400 pt-2 border-t">
                    <div>Created: {{ $product->created_at->format('d M Y, h:i A') }}</div>
                    <div>Updated: {{ $product->updated_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
