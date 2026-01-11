@extends('layouts.admin')

@section('title', 'Stock Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Add Forcefully Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-3xl font-bold text-gray-800">Stock Management</h2>
        <button onclick="openAddStockModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            + Add Forcefully
        </button>
    </div>

    <!-- Products Stock Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($products->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Code</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Current Stock</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->product_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $product->product_name }}</td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $product->stock_quantity ?? 0 }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if(($product->stock_quantity ?? 0) > 10) bg-green-100 text-green-800
                                    @elseif(($product->stock_quantity ?? 0) > 0) bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    @if(($product->stock_quantity ?? 0) > 10)
                                        In Stock
                                    @elseif(($product->stock_quantity ?? 0) > 0)
                                        Low Stock
                                    @else
                                        Out of Stock
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-6 text-center text-gray-500">
                <p>No products found.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Stock Modal -->
<div id="addStockModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-96">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Add Stock Forcefully</h3>

        <form action="{{ route('admin.stocks.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="product_id" class="block text-sm font-semibold text-gray-700 mb-2">Product</label>
                <select id="product_id" name="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_code }} - {{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                <input type="number" id="quantity" name="quantity" value="0" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeAddStockModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Add Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddStockModal() {
    document.getElementById('addStockModal').classList.remove('hidden');
}

function closeAddStockModal() {
    document.getElementById('addStockModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('addStockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddStockModal();
    }
});
</script>
@endsection

