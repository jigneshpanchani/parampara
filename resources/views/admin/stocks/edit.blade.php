@extends('layouts.admin')

@section('title', 'Edit Stock')

@section('content')
<div class="max-w-6xl mx-autoo">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Stock</h2>

        <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Row 1: Product Name & Quantity -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="product_name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name</label>
                    <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $stock->product_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('product_name') border-red-500 @enderror"
                        placeholder="Enter product name" required>
                    @error('product_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $stock->quantity) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror"
                        placeholder="Enter quantity" min="0" required>
                    @error('quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Row 2: Unit Price & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="unit_price" class="block text-sm font-semibold text-gray-700 mb-2">Unit Price</label>
                    <input type="number" id="unit_price" name="unit_price" value="{{ old('unit_price', $stock->unit_price) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('unit_price') border-red-500 @enderror"
                        placeholder="Enter unit price" step="0.01" min="0" required>
                    @error('unit_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                        required>
                        <option value="">Select Status</option>
                        <option value="in_stock" {{ old('status', $stock->status) === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ old('status', $stock->status) === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ old('status', $stock->status) === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                    Update Stock
                </button>
                <a href="{{ route('admin.stocks.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

