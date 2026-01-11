@extends('layouts.admin')

@section('title', 'Create Purchase')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Create New Purchase</h2>

        <form action="{{ route('admin.purchases.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="purchase_date" class="block text-sm font-semibold text-gray-700 mb-2">Purchase Date *</label>
                    <input type="date" id="purchase_date" name="purchase_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('purchase_date', date('Y-m-d')) }}">
                    @error('purchase_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="supplier_name" class="block text-sm font-semibold text-gray-700 mb-2">Supplier Name *</label>
                    <input type="text" id="supplier_name" name="supplier_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('supplier_name') }}">
                    @error('supplier_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="bill_due_date" class="block text-sm font-semibold text-gray-700 mb-2">Bill Due Date</label>
                    <input type="date" id="bill_due_date" name="bill_due_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('bill_due_date') }}">
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Purchase Items</h3>
                <div id="items-container">
                    <div class="item-row grid grid-cols-5 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Product *</label>
                            <select name="products[0][product_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Product</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Quantity *</label>
                            <input type="number" name="products[0][quantity]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 quantity-input" required min="1" value="1">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price *</label>
                            <input type="number" name="products[0][purchase_price]" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 price-input" required min="0">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Line Total</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none row-total" readonly value="0.00">
                        </div>
                        <div class="flex items-end justify-center">
                            <button type="button" class="remove-item text-red-500 hover:text-red-700 font-bold text-xl" style="display:none;" title="Remove">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-item" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">+ Add Item</button>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="transportation_cost" class="block text-sm font-semibold text-gray-700 mb-2">Transportation Cost</label>
                    <input type="number" id="transportation_cost" name="transportation_cost" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('transportation_cost', 0) }}">
                    @error('transportation_cost')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="expense" class="block text-sm font-semibold text-gray-700 mb-2">Expense</label>
                    <input type="number" id="expense" name="expense" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('expense', 0) }}">
                    @error('expense')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="subtotal_amount" class="block text-sm font-semibold text-gray-700 mb-2">Subtotal</label>
                    <input type="text" id="subtotal_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none" readonly value="0.00">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="mb-4">
                    <label for="bill_details" class="block text-sm font-semibold text-gray-700 mb-2">Bill Details</label>
                    <textarea id="bill_details" name="bill_details" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('bill_details') }}</textarea>
                </div>
                <div>
                    <label for="expense_details" class="block text-sm font-semibold text-gray-700 mb-2">Expense Details</label>
                    <textarea id="expense_details" name="expense_details" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('expense_details') }}</textarea>
                    @error('expense_details')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-4">

                <div>
                    <label for="total_amount" class="block text-sm font-semibold text-gray-700 mb-2">Total Amount</label>
                    <input type="text" id="total_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none" readonly value="0.00">
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Create Purchase</button>
                <a href="{{ route('admin.purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    let itemCount = 1;

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
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

    function calculateRowTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const total = quantity * price;
        row.querySelector('.row-total').value = total.toFixed(2);
        updateTotals();
    }

    function attachRowListeners(row) {
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const removeButton = row.querySelector('.remove-item');

        if (quantityInput && !quantityInput.value) {
            quantityInput.value = 1;
        }

        quantityInput.addEventListener('input', () => calculateRowTotal(row));
        priceInput.addEventListener('input', () => calculateRowTotal(row));

        if (removeButton) {
            removeButton.onclick = () => {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    row.remove();
                    updateTotals();
                }
            };
        }

        calculateRowTotal(row);
    }

    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');
        const newItem = document.querySelector('.item-row').cloneNode(true);

        newItem.querySelectorAll('input, select').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${itemCount}]`);
            if (el.classList.contains('row-total')) {
                el.value = '0.00';
            } else if (el.classList.contains('quantity-input')) {
                el.value = '1';
            } else {
                el.value = '';
            }
        });

        const removeButton = newItem.querySelector('.remove-item');
        if (removeButton) {
            removeButton.style.display = 'block';
        }

        container.appendChild(newItem);
        attachRowListeners(newItem);
        itemCount++;
    });

    document.getElementById('transportation_cost').addEventListener('input', updateTotals);
    document.getElementById('expense').addEventListener('input', updateTotals);

    document.querySelectorAll('.item-row').forEach(row => {
        attachRowListeners(row);
    });
</script>
@endsection
