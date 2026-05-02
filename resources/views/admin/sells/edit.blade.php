@extends('layouts.admin')

@section('title', 'Edit Sale')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Sale</h2>

        <form action="{{ route('admin.sells.update', $sell) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="sell_date" class="block text-sm font-semibold text-gray-700 mb-2">Sale Date *</label>
                    <input type="date" id="sell_date" name="sell_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ old('sell_date', $sell->sell_date->format('Y-m-d')) }}">
                    @error('sell_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="seller_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Seller Name
                        <span id="seller_name_hint_optional" class="text-gray-500 text-xs">(Optional)</span>
                        <span id="seller_name_hint_required" class="text-red-500 text-xs" style="display: none;">*</span>
                    </label>
                    <input type="text" id="seller_name" name="seller_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter seller name" value="{{ old('seller_name', $sell->seller_name) }}">
                    @error('seller_name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="seller_contact_number" class="block text-sm font-semibold text-gray-700 mb-2">Seller Contact Number <span class="text-gray-500 text-xs">(Optional)</span></label>
                    <input type="text" id="seller_contact_number" name="seller_contact_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter contact number" value="{{ old('seller_contact_number', $sell->seller_contact_number) }}">
                    @error('seller_contact_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Products Section -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Products</h3>
                    <button type="button" onclick="addProductRow()" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">+ Add Product</button>
                </div>

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
                            @foreach($sell->items as $item)
                                <tr class="product-row border-b">
                                    <td class="px-4 py-2">
                                        <select name="product_id[]" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="quantity[]" class="w-full px-3 py-2 border border-gray-300 rounded quantity-input" min="1" value="{{ $item->quantity }}" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="selling_price[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded price-input" min="0" value="{{ $item->selling_price }}" required>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 row-total" readonly value="{{ number_format($item->total_price, 2, '.', '') }}">
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" onclick="removeProductRow(this)" class="text-red-500 hover:text-red-700 font-bold">Remove</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="total_amount" class="block text-sm font-semibold text-gray-700 mb-2">Total Amount</label>
                    <input type="text" id="total_amount" name="total_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none" readonly value="{{ old('total_amount', number_format($sell->total_amount, 2, '.', '')) }}">
                </div>
                <div>
                    <label for="payment_mode" class="block text-sm font-semibold text-gray-700 mb-2">Payment Mode <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                    <select id="payment_mode" name="payment_mode" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Pay Later —</option>
                        <option value="cash" {{ old('payment_mode', $displayPaymentMode) === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="upi" {{ old('payment_mode', $displayPaymentMode) === 'upi' ? 'selected' : '' }}>UPI</option>
                        <option value="gpay" {{ old('payment_mode', $displayPaymentMode) === 'gpay' ? 'selected' : '' }}>G-pay</option>
                        <option value="mix" {{ old('payment_mode', $displayPaymentMode) === 'mix' ? 'selected' : '' }}>Mix (Cash & Online)</option>
                    </select>
                    @error('payment_mode')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div id="amount_paid_container">
                    <label for="amount_paid" class="block text-sm font-semibold text-gray-700 mb-2">Amount Paid *</label>
                    <input type="number" id="amount_paid" name="amount_paid" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required min="0" value="{{ old('amount_paid', $displayAmountPaid) }}">
                    @error('amount_paid')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Cash Amount (shown only when payment_mode is 'mix') -->
                <div id="cash_amount_container" style="display: none;">
                    <label for="cash_amount" class="block text-sm font-semibold text-gray-700 mb-2">Cash Amount *</label>
                    <input type="number" id="cash_amount" name="cash_amount" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="{{ old('cash_amount', $displayCashAmount) }}">
                    @error('cash_amount')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div id="online_amount_container" style="display: none;">
                    <label for="online_amount" class="block text-sm font-semibold text-gray-700 mb-2">Online Amount *</label>
                    <input type="number" id="online_amount" name="online_amount" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" min="0" value="{{ old('online_amount', $displayOnlineAmount) }}">
                    @error('online_amount')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $sell->notes) }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Update Sale</button>
                <a href="{{ route('admin.sells.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
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
                    <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}">{{ $product->product_name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="quantity[]" class="w-full px-3 py-2 border border-gray-300 rounded quantity-input" min="1" value="1" required>
        </td>
        <td class="px-4 py-2">
            <input type="number" name="selling_price[]" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded price-input" min="0" required>
        </td>
        <td class="px-4 py-2">
            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-100 row-total" readonly>
        </td>
        <td class="px-4 py-2 text-center">
            <button type="button" onclick="removeProductRow(this)" class="text-red-500 hover:text-red-700 font-bold">Remove</button>
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
    row.querySelector('.row-total').value = total.toFixed(2);
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

function updateTotals() {
    let totalAmount = 0;
    document.querySelectorAll('.product-row').forEach(row => {
        const rowTotal = parseFloat(row.querySelector('.row-total').value) || 0;
        totalAmount += rowTotal;
    });

    const totalAmountInput = document.getElementById('total_amount');
    if (totalAmountInput) {
        totalAmountInput.value = totalAmount.toFixed(2);
    }
}

function attachRowListeners(row) {
    const quantityInput = row.querySelector('.quantity-input');
    const priceInput = row.querySelector('.price-input');
    const productSelect = row.querySelector('select[name="product_id[]"]');

    if (!quantityInput.value) {
        quantityInput.value = 1;
    }

    quantityInput.addEventListener('input', () => calculateRowTotal(row));
    priceInput.addEventListener('input', () => calculateRowTotal(row));
    productSelect.addEventListener('change', () => synchronizePrice(row));

    if (productSelect.value && !priceInput.value) {
        synchronizePrice(row);
    } else {
        calculateRowTotal(row);
    }
}

// Attach listeners to initial rows and sync totals
if (document.querySelectorAll('.product-row').length) {
    document.querySelectorAll('.product-row').forEach(row => {
        attachRowListeners(row);
    });
    updateTotals();
}

// Handle payment mode change
const paymentModeSelect = document.getElementById('payment_mode');
const cashAmountContainer = document.getElementById('cash_amount_container');
const onlineAmountContainer = document.getElementById('online_amount_container');
const amountPaidContainer = document.getElementById('amount_paid_container');
const amountPaidInput = document.getElementById('amount_paid');
const cashAmountInput = document.getElementById('cash_amount');
const onlineAmountInput = document.getElementById('online_amount');
const sellerNameInput = document.getElementById('seller_name');
const sellerNameHintOptional = document.getElementById('seller_name_hint_optional');
const sellerNameHintRequired = document.getElementById('seller_name_hint_required');

function handlePaymentModeChange() {
    const paymentMode = paymentModeSelect.value;

    if (paymentMode === 'mix') {
        // Show cash and online amount fields, hide amount paid
        cashAmountContainer.style.display = 'block';
        onlineAmountContainer.style.display = 'block';
        amountPaidContainer.style.display = 'none';
        amountPaidInput.removeAttribute('required');
        amountPaidInput.removeAttribute('min');
        cashAmountInput.setAttribute('required', 'required');
        onlineAmountInput.setAttribute('required', 'required');
    } else {
        // Hide cash and online amount fields, show amount paid
        cashAmountContainer.style.display = 'none';
        onlineAmountContainer.style.display = 'none';
        amountPaidContainer.style.display = 'block';
        amountPaidInput.setAttribute('required', 'required');
        cashAmountInput.removeAttribute('required');
        onlineAmountInput.removeAttribute('required');

        // cash/upi/gpay => Amount Paid must be > 0; Pay Later allows 0
        if (paymentMode === 'cash' || paymentMode === 'upi' || paymentMode === 'gpay') {
            amountPaidInput.setAttribute('min', '0.01');
        } else {
            amountPaidInput.setAttribute('min', '0');
        }
    }

    // Pay Later (empty) => Seller Name required
    if (paymentMode === '') {
        sellerNameInput.setAttribute('required', 'required');
        sellerNameHintOptional.style.display = 'none';
        sellerNameHintRequired.style.display = 'inline';
    } else {
        sellerNameInput.removeAttribute('required');
        sellerNameHintOptional.style.display = 'inline';
        sellerNameHintRequired.style.display = 'none';
    }
}

function validatePaymentAmounts() {
    const paymentMode = paymentModeSelect.value;

    if (paymentMode === 'cash' || paymentMode === 'upi' || paymentMode === 'gpay') {
        const paid = parseFloat(amountPaidInput.value) || 0;
        if (paid <= 0) {
            alert('Amount Paid must be greater than 0 for ' + paymentMode.toUpperCase() + ' payment.');
            amountPaidInput.focus();
            return false;
        }
    } else if (paymentMode === 'mix') {
        const cash = parseFloat(cashAmountInput.value) || 0;
        const online = parseFloat(onlineAmountInput.value) || 0;
        if (cash + online <= 0) {
            alert('For Mix payment, Cash Amount or Online Amount must be greater than 0.');
            cashAmountInput.focus();
            return false;
        }
    }
    return true;
}

document.querySelector('form[action="{{ route('admin.sells.update', $sell) }}"]').addEventListener('submit', function(event) {
    if (!validatePaymentAmounts()) {
        event.preventDefault();
    }
});

function updateAmountPaidFromMix() {
    const cashAmount = parseFloat(cashAmountInput.value) || 0;
    const onlineAmount = parseFloat(onlineAmountInput.value) || 0;
    amountPaidInput.value = (cashAmount + onlineAmount).toFixed(2);
}

paymentModeSelect.addEventListener('change', handlePaymentModeChange);
cashAmountInput.addEventListener('input', updateAmountPaidFromMix);
onlineAmountInput.addEventListener('input', updateAmountPaidFromMix);

// Initialize on page load
handlePaymentModeChange();
</script>
@endsection

