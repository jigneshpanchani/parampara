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
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Code</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Base Price Range</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Selling Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Stock</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Photo</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $index => $product)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-4 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->product_code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->base_price_range_formatted }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 selling-price-cell">{{ $product->selling_price_formatted }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php $status = $product->getStockStatus(); @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($status === 'in_stock') bg-green-100 text-green-800
                                @elseif($status === 'low_stock') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $product->getCurrentStock() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($product->photo)
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->product_name }}" class="h-10 w-10 rounded object-cover">
                            @else
                                <span class="text-gray-400">No photo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <label class="status-toggle inline-flex items-center cursor-pointer" title="Toggle active status">
                                <input type="checkbox" class="sr-only status-toggle-input" data-product-id="{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }}>
                                <span class="status-toggle-track relative w-11 h-6 rounded-full transition-colors {{ $product->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                    <span class="status-toggle-thumb absolute top-0.5 left-0.5 bg-white w-5 h-5 rounded-full shadow transition-transform {{ $product->is_active ? 'translate-x-5' : '' }}"></span>
                                </span>
                            </label>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-3 flex items-center">
                            <button type="button" class="selling-price-btn text-green-600 hover:text-green-800 text-xl font-bold transition" title="Quick Update Selling Price"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->product_name }}"
                                data-base-price-range="{{ $product->base_price_range_formatted }}"
                                data-selling-price="{{ $product->selling_price }}">
                                ₹
                            </button>
                            <a href="{{ route('admin.products.show', $product) }}" class="text-gray-500 hover:text-gray-700 text-xl transition" title="View Product">
                                👁️
                            </a>
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

<!-- Selling Price Update Modal -->
<div id="sellingPriceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 id="sellingPriceModalTitle" class="text-xl font-bold text-gray-800"></h3>
            <button type="button" onclick="closeSellingPriceModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <p class="text-gray-600 text-sm mb-2">Base Price Range</p>
        <p id="sellingPriceModalBaseRange" class="text-gray-800 font-semibold mb-4"></p>
        <form id="sellingPriceForm" onsubmit="updateSellingPrice(event)">
            @csrf
            <input type="hidden" id="sellingPriceProductId" name="product_id">
            <div class="mb-4">
                <label for="sellingPriceInput" class="block text-sm font-semibold text-gray-700 mb-2">Selling Price</label>
                <input type="number" id="sellingPriceInput" name="selling_price" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
            </div>
            <div id="sellingPriceError" class="hidden mb-4 p-2 bg-red-50 text-red-600 text-sm rounded"></div>
            <div id="sellingPriceSuccess" class="hidden mb-4 p-2 bg-green-50 text-green-600 text-sm rounded"></div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeSellingPriceModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" id="sellingPriceSubmitBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                    Update Selling Price
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openSellingPriceModal(productId, productName, basePriceRange, sellingPrice) {
    document.getElementById('sellingPriceModalTitle').textContent = productName;
    document.getElementById('sellingPriceModalBaseRange').textContent = basePriceRange;
    document.getElementById('sellingPriceProductId').value = productId;
    document.getElementById('sellingPriceInput').value = sellingPrice;
    document.getElementById('sellingPriceError').classList.add('hidden');
    document.getElementById('sellingPriceSuccess').classList.add('hidden');
    document.getElementById('sellingPriceModal').classList.remove('hidden');
    document.getElementById('sellingPriceInput').focus();
}

function closeSellingPriceModal() {
    document.getElementById('sellingPriceModal').classList.add('hidden');
}

function updateSellingPrice(event) {
    event.preventDefault();
    const productId = document.getElementById('sellingPriceProductId').value;
    const sellingPrice = document.getElementById('sellingPriceInput').value;
    const errorEl = document.getElementById('sellingPriceError');
    const successEl = document.getElementById('sellingPriceSuccess');
    const submitBtn = document.getElementById('sellingPriceSubmitBtn');

    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');
    submitBtn.disabled = true;

    const url = `/admin/products/${productId}/selling-price`;
    const token = document.querySelector('#sellingPriceForm input[name="_token"]').value;

    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ selling_price: parseFloat(sellingPrice) }),
    })
    .then(response => response.json().then(data => ({ ok: response.ok, data })))
    .then(({ ok, data }) => {
        if (ok && data.success) {
            successEl.textContent = data.message;
            successEl.classList.remove('hidden');
            const row = document.querySelector(`button.selling-price-btn[data-product-id="${productId}"]`)?.closest('tr');
            if (row) {
                const sellingPriceCell = row.querySelector('.selling-price-cell');
                if (sellingPriceCell) {
                    sellingPriceCell.textContent = '₹' + parseFloat(data.selling_price).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }
            setTimeout(closeSellingPriceModal, 1000);
        } else {
            const msg = data?.errors?.selling_price?.[0] || data?.message || 'Failed to update selling price.';
            errorEl.textContent = msg;
            errorEl.classList.remove('hidden');
        }
    })
    .catch(() => {
        errorEl.textContent = 'An error occurred. Please try again.';
        errorEl.classList.remove('hidden');
    })
    .finally(() => {
        submitBtn.disabled = false;
    });
}

document.querySelectorAll('.selling-price-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        openSellingPriceModal(
            this.dataset.productId,
            this.dataset.productName,
            this.dataset.basePriceRange,
            this.dataset.sellingPrice
        );
    });
});

document.getElementById('sellingPriceModal')?.addEventListener('click', function(event) {
    if (event.target === this) closeSellingPriceModal();
});
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && !document.getElementById('sellingPriceModal').classList.contains('hidden')) {
        closeSellingPriceModal();
    }
});

document.querySelectorAll('.status-toggle-input').forEach(input => {
    input.addEventListener('change', function() {
        const productId = this.dataset.productId;
        const checkbox = this;
        const label = this.closest('.status-toggle');
        const track = label.querySelector('.status-toggle-track');
        const thumb = label.querySelector('.status-toggle-thumb');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value;

        checkbox.disabled = true;

        fetch(`/admin/products/${productId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (ok && data.success) {
                const isActive = !!data.is_active;
                checkbox.checked = isActive;
                track.classList.toggle('bg-green-500', isActive);
                track.classList.toggle('bg-gray-300', !isActive);
                thumb.classList.toggle('translate-x-5', isActive);
            } else {
                checkbox.checked = !checkbox.checked;
                alert(data?.message || 'Failed to update status.');
            }
        })
        .catch(() => {
            checkbox.checked = !checkbox.checked;
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            checkbox.disabled = false;
        });
    });
});
</script>
@endsection

