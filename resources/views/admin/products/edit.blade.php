@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Product</h2>

        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="product_name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                    <input type="text" id="product_name" name="product_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('product_name', $product->product_name) }}">
                    @error('product_name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="product_code" class="block text-sm font-semibold text-gray-700 mb-2">Product Code *</label>
                    <input type="text" id="product_code" name="product_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('product_code', $product->product_code) }}">
                    @error('product_code')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="base_price_min" class="block text-sm font-semibold text-gray-700 mb-2">Base Price Min *</label>
                    <input type="number" id="base_price_min" name="base_price_min" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('base_price_min', $product->base_price_min) }}">
                    @error('base_price_min')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="base_price_max" class="block text-sm font-semibold text-gray-700 mb-2">Base Price Max *</label>
                    <input type="number" id="base_price_max" name="base_price_max" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('base_price_max', $product->base_price_max) }}">
                    @error('base_price_max')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="sell_price" class="block text-sm font-semibold text-gray-700 mb-2">Sell Price *</label>
                    <input type="number" id="sell_price" name="sell_price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('sell_price', $product->sell_price) }}">
                    @error('sell_price')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <div class="mb-4">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                @error('description')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">Product Photo</label>
                @if ($product->photo)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->product_name }}" class="h-20 w-20 rounded">
                    </div>
                @endif
                <input type="file" id="photo" name="photo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('photo')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Update Product</button>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

