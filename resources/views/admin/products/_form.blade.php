@props(['product' => null])

<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="mb-4">
        <label for="product_name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
        <input type="text" id="product_name" name="product_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('product_name', $product?->product_name) }}">
        @error('product_name')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="product_code" class="block text-sm font-semibold text-gray-700 mb-2">Product Code *</label>
        <input type="text" id="product_code" name="product_code" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('product_code', $product?->product_code) }}">
        @error('product_code')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="grid grid-cols-3 gap-4 mb-4">
    <div>
        <label for="base_price_min" class="block text-sm font-semibold text-gray-700 mb-2">Base Price Min *</label>
        <input type="number" id="base_price_min" name="base_price_min" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('base_price_min', $product?->base_price_min) }}">
        @error('base_price_min')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="base_price_max" class="block text-sm font-semibold text-gray-700 mb-2">Base Price Max *</label>
        <input type="number" id="base_price_max" name="base_price_max" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('base_price_max', $product?->base_price_max) }}">
        @error('base_price_max')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
    <div>
        <label for="selling_price" class="block text-sm font-semibold text-gray-700 mb-2">Selling Price *</label>
        <input type="number" id="selling_price" name="selling_price" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('selling_price', $product?->selling_price) }}">
        @error('selling_price')
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product?->description) }}</textarea>
    @error('description')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

<div class="mb-6">
    <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">Product Photo</label>
    @if ($product?->photo)
        <div class="mb-2">
            <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->product_name }}" class="h-20 w-20 rounded object-cover">
        </div>
    @endif
    <input type="file" id="photo" name="photo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
    @error('photo')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>

<div class="mb-6">
    @php $isActive = old('is_active', $product?->is_active ?? true); @endphp
    <label class="inline-flex items-center cursor-pointer">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" id="is_active" name="is_active" value="1" class="sr-only peer" {{ $isActive ? 'checked' : '' }}>
        <span class="relative w-11 h-6 rounded-full transition-colors bg-gray-300 peer-checked:bg-green-500 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:shadow after:transition-transform peer-checked:after:translate-x-5"></span>
        <span class="ml-3 text-sm font-semibold text-gray-700">Active</span>
    </label>
    <p class="mt-1 text-xs text-gray-500">Inactive products are hidden from sell and purchase forms.</p>
    @error('is_active')
        <span class="text-red-500 text-sm">{{ $message }}</span>
    @enderror
</div>
