@extends('layouts.admin')

@section('title', 'Edit Purchase')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Purchase</h2>

        <form action="{{ route('admin.purchases.update', $purchase) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="purchase_date" class="block text-sm font-semibold text-gray-700 mb-2">Purchase Date *</label>
                    <input type="date" id="purchase_date" name="purchase_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}">
                    @error('purchase_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="supplier_name" class="block text-sm font-semibold text-gray-700 mb-2">Supplier Name *</label>
                    <input type="text" id="supplier_name" name="supplier_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('supplier_name', $purchase->supplier_name) }}">
                    @error('supplier_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="bill_due_date" class="block text-sm font-semibold text-gray-700 mb-2">Bill Due Date</label>
                    <input type="date" id="bill_due_date" name="bill_due_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('bill_due_date', $purchase->bill_due_date?->format('Y-m-d')) }}">
                    @error('bill_due_date')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>



            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchase Items</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Product</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Quantity</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Price</th>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Total</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            @foreach($purchase->items as $item)
                                <tr class="product-row border-b">
                                    <td class="px-4 py-2">
                                        <select name="product_id[]" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->base_price_min }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="quantity[]" class="w-full px-3 py-2 border border-gray-300 rounded quantity-input" min="1" value="{{ $item->quantity }}" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="purchase_price[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded price-input" min="0" value="{{ $item->purchase_price }}" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 row-total" readonly value="{{ $item->total_price }}">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" onclick="removeProductRow(this)" class="text-red-500 hover:text-red-700 font-bold" title="Remove">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" id="add-product-row" class="mt-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">+ Add Item</button>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="transportation_cost" class="block text-sm font-semibold text-gray-700 mb-2">Transportation Cost</label>
                    <input type="number" id="transportation_cost" name="transportation_cost" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('transportation_cost', $purchase->transportation_cost) }}">
                    @error('transportation_cost')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="expense" class="block text-sm font-semibold text-gray-700 mb-2">Expense</label>
                    <input type="number" id="expense" name="expense" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('expense', $purchase->expense) }}">
                    @error('expense')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="subtotal_amount" class="block text-sm font-semibold text-gray-700 mb-2">Subtotal</label>
                    <input type="text" id="subtotal_amount" name="subtotal_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none" readonly value="0.00">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="mb-4">
                    <label for="bill_details" class="block text-sm font-semibold text-gray-700 mb-2">Bill Details</label>
                    <textarea id="bill_details" name="bill_details" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('bill_details', $purchase->bill_details) }}</textarea>
                </div>
                <div>
                    <label for="expense_details" class="block text-sm font-semibold text-gray-700 mb-2">Expense Details</label>
                    <textarea id="expense_details" name="expense_details" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('expense_details', $purchase->expense_details) }}</textarea>
                    @error('expense_details')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="total_amount" class="block text-sm font-semibold text-gray-700 mb-2">Total Amount</label>
                    <input type="text" id="total_amount" name="total_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none" readonly value="{{ old('total_amount', number_format($purchase->total_amount, 2)) }}">
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Update Purchase</button>
                <a href="{{ route('admin.purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function addProductRow() {
    const productRows = document.getElementById('productRows');
    const newRow = document.createElement('tr');
    newRow.className = 'product-row border-b';
    newRow.innerHTML = `
        <td class="px-4 py-2">
            <select name="product_id[]" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                <option value="">Select Product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->base_price_min }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="quantity[]" class="w-full px-3 py-2 border border-gray-300 rounded quantity-input" min="1" value="1" required>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="purchase_price[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded price-input" min="0" required>
        </td>
        <td class="px-4 py-2">
            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 row-total" readonly>
        </td>
        <td class="px-4 py-2 text-center">
            <button type="button" onclick="removeProductRow(this)" class="text-red-500 hover:text-red-700 font-bold" title="Remove">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        </td>
    `;
    productRows.appendChild(newRow);
    attachRowListeners(newRow);
    updateTotals();
}

function removeProductRow(button) {
    const rows = document.querySelectorAll('.product-row');
    if (rows.length > 1) {
        button.closest('tr').remove();
        updateTotals();
    } else {
        alert('At least one product is required');
    }
}

function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
    const price = parseFloat(row.querySelector('.price-input').value) || 0;
    const total = quantity * price;
    const rowTotalInput = row.querySelector('.row-total');
    rowTotalInput.value = total.toFixed(2);
    updateTotals();
}

function synchronizePrice(row) {
    const productSelect = row.querySelector('select[name="product_id[]"]');
    const priceInput = row.querySelector('.price-input');
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    const optionPrice = selectedOption ? selectedOption.dataset.price : null;

    if (optionPrice) {
        priceInput.value = parseFloat(optionPrice).toFixed(2);
    } else {
        priceInput.value = '';
    }

    calculateRowTotal(row);
}

function attachRowListeners(row) {
    const productSelect = row.querySelector('select[name="product_id[]"]');
    const quantityInput = row.querySelector('.quantity-input');
    const priceInput = row.querySelector('.price-input');

    productSelect.addEventListener('change', () => synchronizePrice(row));
    quantityInput.addEventListener('input', () => calculateRowTotal(row));
    priceInput.addEventListener('input', () => calculateRowTotal(row));
}

function updateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.product-row').forEach(row => {
        const rowTotal = parseFloat(row.querySelector('.row-total').value) || 0;
        subtotal += rowTotal;
    });

    const subtotalInput = document.getElementById('subtotal_amount');
    if (subtotalInput) {
        subtotalInput.value = subtotal.toFixed(2);
    }

    const transportation = parseFloat(document.getElementById('transportation_cost').value) || 0;
    const expense = parseFloat(document.getElementById('expense').value) || 0;
    const totalAmount = subtotal + transportation + expense;

    const totalAmountInput = document.getElementById('total_amount');
    if (totalAmountInput) {
        totalAmountInput.value = totalAmount.toFixed(2);
    }
}

document.getElementById('add-product-row').addEventListener('click', addProductRow);
document.getElementById('transportation_cost').addEventListener('input', updateTotals);
document.getElementById('expense').addEventListener('input', updateTotals);

document.querySelectorAll('.product-row').forEach(row => {
    attachRowListeners(row);
});

// Initialize totals on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
});
</script>
@endsection

