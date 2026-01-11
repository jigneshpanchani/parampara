@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">📦 Products</h2>
    <a href="{{ route('admin.products.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        + Add New Product
    </a>
</div>

@if ($products->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No products found. <a href="{{ route('admin.products.create') }}" class="text-blue-500 hover:underline">Create one now</a></p>
    </div>
@else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Code</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Base Price Range</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sell Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Photo</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->product_code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($product->base_price_min, 2) }} - ₹{{ number_format($product->base_price_max, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($product->sell_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->product_name }}" class="h-10 w-10 rounded">
                            @else
                                <span class="text-gray-400">No photo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm space-x-3 flex items-center">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-500 hover:text-blue-700 text-xl transition" title="Edit Product">
                                ✏️
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline delete-form" data-item-name="Product: {{ $product->product_name }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xl transition" title="Delete Product">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

