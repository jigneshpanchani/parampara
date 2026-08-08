@extends('layouts.admin')

@section('title', 'New Tax Invoice')

@php
    $units = config('tax_invoice.units', []);
    $gstRates = config('tax_invoice.gst_rates', [0, 5, 12, 18, 28]);
    // Rows to render initially: repopulate from old() on validation error, else one empty row.
    $oldNames = old('product_name', [null]);
    $rowCount = max(1, count($oldNames));
@endphp

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">New Tax Invoice</h2>
            <a href="{{ route('admin.tax-invoices.index') }}" class="text-gray-600 hover:underline text-sm">← All tax invoices</a>
        </div>

        @if (! $company || ! $company->state_code)
            <div class="mb-6 p-3 rounded bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm">
                Tip: set your company <strong>State</strong>, GSTIN and bank details in
                <a href="{{ route('admin.settings.index') }}" class="underline">Settings</a> so they appear on the invoice.
            </div>
        @endif

        <form action="{{ route('admin.tax-invoices.store') }}" method="POST" id="taxInvoiceForm">
            @csrf

            {{-- Buyer + invoice meta --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                {{-- Buyer --}}
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">Buyer (M/s)</h3>
                    <div class="mb-3">
                        <label for="buyer_name" class="block text-sm font-semibold text-gray-700 mb-1">Buyer Name *</label>
                        <input type="text" id="buyer_name" name="buyer_name" list="buyer_names" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('buyer_name') }}" placeholder="Customer / firm name">
                        <datalist id="buyer_names">
                            @foreach ($buyerNames as $name)
                                <option value="{{ $name }}"></option>
                            @endforeach
                        </datalist>
                        @error('buyer_name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="buyer_address" class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                        <textarea id="buyer_address" name="buyer_address" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('buyer_address') }}</textarea>
                        @error('buyer_address')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="buyer_gstin" class="block text-sm font-semibold text-gray-700 mb-1">GSTIN</label>
                            <input type="text" id="buyer_gstin" name="buyer_gstin"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('buyer_gstin') }}">
                            @error('buyer_gstin')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="buyer_state" class="block text-sm font-semibold text-gray-700 mb-1">Place of Supply / State</label>
                            <input type="text" id="buyer_state" name="buyer_state"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('buyer_state', $company->state_code ?? '') }}" placeholder="e.g. 24-Gujarat">
                            @error('buyer_state')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="buyer_contact_number" class="block text-sm font-semibold text-gray-700 mb-1">Contact Number</label>
                        <input type="text" id="buyer_contact_number" name="buyer_contact_number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('buyer_contact_number') }}">
                    </div>
                </div>

                {{-- Invoice meta --}}
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-3">Invoice Details</h3>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label for="invoice_date" class="block text-sm font-semibold text-gray-700 mb-1">Invoice Date *</label>
                            <input type="date" id="invoice_date" name="invoice_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('invoice_date', date('Y-m-d')) }}">
                            @error('invoice_date')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="eway_bill_no" class="block text-sm font-semibold text-gray-700 mb-1">E-Way Bill No</label>
                            <input type="text" id="eway_bill_no" name="eway_bill_no"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('eway_bill_no') }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label for="vehicle_no" class="block text-sm font-semibold text-gray-700 mb-1">Vehicle No</label>
                            <input type="text" id="vehicle_no" name="vehicle_no"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('vehicle_no') }}">
                        </div>
                        <div>
                            <label for="transport" class="block text-sm font-semibold text-gray-700 mb-1">Transport</label>
                            <input type="text" id="transport" name="transport"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                value="{{ old('transport') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="broker" class="block text-sm font-semibold text-gray-700 mb-1">Broker</label>
                        <input type="text" id="broker" name="broker"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ old('broker') }}">
                    </div>
                    <label class="inline-flex items-center cursor-pointer mt-1">
                        <input type="hidden" name="is_interstate" value="0">
                        <input type="checkbox" id="is_interstate" name="is_interstate" value="1" class="sr-only peer" {{ old('is_interstate') ? 'checked' : '' }}>
                        <span class="relative w-11 h-6 rounded-full transition-colors bg-gray-300 peer-checked:bg-blue-500 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:w-5 after:h-5 after:rounded-full after:shadow after:transition-transform peer-checked:after:translate-x-5"></span>
                        <span class="ml-3 text-sm font-semibold text-gray-700">Inter-state sale (IGST)</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500">Off → CGST + SGST (same state). On → IGST (other state).</p>
                </div>
            </div>

            {{-- Products --}}
            <div class="mb-4">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold text-gray-800">Products</h3>
                    <button type="button" id="addRowBtn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">+ Add Product</button>
                </div>
                @error('product_name')<div class="mb-2 text-red-500 text-sm">{{ $message }}</div>@enderror

                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-300 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-2 py-2 text-left font-semibold" style="min-width: 220px;">Product</th>
                                <th class="px-2 py-2 text-left font-semibold">HSN/SAC</th>
                                <th class="px-2 py-2 text-left font-semibold">Qty</th>
                                <th class="px-2 py-2 text-left font-semibold">Unit</th>
                                <th class="px-2 py-2 text-left font-semibold">Rate</th>
                                <th class="px-2 py-2 text-left font-semibold">GST %</th>
                                <th class="px-2 py-2 text-right font-semibold">Amount</th>
                                <th class="px-2 py-2 text-center font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productRows">
                            @for ($i = 0; $i < $rowCount; $i++)
                                <tr class="product-row border-b">
                                    <td class="px-2 py-2">
                                        <select class="product-select w-full px-2 py-2 border border-gray-300 rounded mb-1">
                                            <option value="">— Pick to autofill —</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-name="{{ $product->product_name }}"
                                                    data-hsn="{{ $product->hsn_code }}"
                                                    data-unit="{{ $product->unit_of_measure }}"
                                                    data-gst="{{ $product->gst_rate }}"
                                                    data-price="{{ $product->selling_price }}"
                                                    {{ (string) old("product_id.$i") === (string) $product->id ? 'selected' : '' }}>
                                                    {{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="product_id[]" class="product-id" value="{{ old("product_id.$i") }}">
                                        <input type="text" name="product_name[]" class="product-name w-full px-2 py-2 border border-gray-300 rounded" required placeholder="Product name" value="{{ old("product_name.$i") }}">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" name="hsn_code[]" class="hsn-input w-24 px-2 py-2 border border-gray-300 rounded" value="{{ old("hsn_code.$i") }}">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="quantity[]" step="0.001" min="0" class="qty-input w-24 px-2 py-2 border border-gray-300 rounded" required value="{{ old("quantity.$i", 1) }}">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="text" name="unit_of_measure[]" list="unit_options" class="unit-input w-24 px-2 py-2 border border-gray-300 rounded" value="{{ old("unit_of_measure.$i") }}">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="rate[]" step="0.01" min="0" class="rate-input w-28 px-2 py-2 border border-gray-300 rounded" required value="{{ old("rate.$i") }}">
                                    </td>
                                    <td class="px-2 py-2">
                                        <input type="number" name="gst_rate[]" step="0.01" min="0" max="100" class="gst-input w-20 px-2 py-2 border border-gray-300 rounded" required value="{{ old("gst_rate.$i", 0) }}">
                                    </td>
                                    <td class="px-2 py-2 text-right">
                                        <input type="text" class="row-amount w-28 px-2 py-2 border border-gray-300 rounded bg-gray-100 text-right" readonly>
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" class="remove-row text-red-500 hover:text-red-700 font-bold">Remove</button>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <datalist id="unit_options">
                    @foreach ($units as $unit)
                        <option value="{{ $unit }}"></option>
                    @endforeach
                </datalist>
            </div>

            {{-- Totals --}}
            <div class="flex justify-end mb-6">
                <div class="w-full md:w-80 border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between py-1 text-sm">
                        <span class="text-gray-600">Taxable Amount</span>
                        <span class="font-semibold" id="sumTaxable">₹0.00</span>
                    </div>
                    <div class="flex justify-between py-1 text-sm intra-tax">
                        <span class="text-gray-600">CGST</span>
                        <span class="font-semibold" id="sumCgst">₹0.00</span>
                    </div>
                    <div class="flex justify-between py-1 text-sm intra-tax">
                        <span class="text-gray-600">SGST</span>
                        <span class="font-semibold" id="sumSgst">₹0.00</span>
                    </div>
                    <div class="flex justify-between py-1 text-sm inter-tax" style="display:none;">
                        <span class="text-gray-600">IGST</span>
                        <span class="font-semibold" id="sumIgst">₹0.00</span>
                    </div>
                    <div class="flex justify-between py-2 mt-1 border-t border-gray-300 text-base">
                        <span class="font-bold text-gray-800">Grand Total</span>
                        <span class="font-bold text-gray-900" id="sumGrand">₹0.00</span>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">Create Invoice</button>
                <a href="{{ route('admin.tax-invoices.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">Cancel</a>
            </div>
        </form>
    </div>
</div>

{{-- Product <option> markup reused when adding new rows --}}
<template id="productOptionsTemplate">
    <option value="">— Pick to autofill —</option>
    @foreach ($products as $product)
        <option value="{{ $product->id }}"
            data-name="{{ $product->product_name }}"
            data-hsn="{{ $product->hsn_code }}"
            data-unit="{{ $product->unit_of_measure }}"
            data-gst="{{ $product->gst_rate }}"
            data-price="{{ $product->selling_price }}">{{ $product->product_name }}</option>
    @endforeach
</template>

<script>
(function () {
    'use strict';

    const productRows = document.getElementById('productRows');
    const addRowBtn = document.getElementById('addRowBtn');
    const interstateToggle = document.getElementById('is_interstate');
    const optionsHtml = document.getElementById('productOptionsTemplate').innerHTML;

    function formatMoney(n) {
        return '₹' + (Number(n) || 0).toFixed(2);
    }

    function rowAmount(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
        return qty * rate;
    }

    function recalcRow(row) {
        row.querySelector('.row-amount').value = rowAmount(row).toFixed(2);
        recalcTotals();
    }

    function recalcTotals() {
        const interstate = interstateToggle.checked;
        let taxable = 0, cgst = 0, sgst = 0, igst = 0;

        document.querySelectorAll('.product-row').forEach(function (row) {
            const amount = rowAmount(row);
            const gst = parseFloat(row.querySelector('.gst-input').value) || 0;
            const tax = amount * gst / 100;
            taxable += amount;
            if (interstate) {
                igst += tax;
            } else {
                const half = tax / 2;
                cgst += half;
                sgst += tax - half;
            }
        });

        const grand = taxable + cgst + sgst + igst;

        document.getElementById('sumTaxable').textContent = formatMoney(taxable);
        document.getElementById('sumCgst').textContent = formatMoney(cgst);
        document.getElementById('sumSgst').textContent = formatMoney(sgst);
        document.getElementById('sumIgst').textContent = formatMoney(igst);
        document.getElementById('sumGrand').textContent = formatMoney(grand);

        document.querySelectorAll('.intra-tax').forEach(el => el.style.display = interstate ? 'none' : 'flex');
        document.querySelectorAll('.inter-tax').forEach(el => el.style.display = interstate ? 'flex' : 'none');
    }

    function onProductSelect(row) {
        const select = row.querySelector('.product-select');
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            row.querySelector('.product-id').value = '';
            return;
        }
        row.querySelector('.product-id').value = opt.value;
        row.querySelector('.product-name').value = opt.dataset.name || '';
        row.querySelector('.hsn-input').value = opt.dataset.hsn || '';
        row.querySelector('.unit-input').value = opt.dataset.unit || '';
        if (opt.dataset.price) {
            row.querySelector('.rate-input').value = parseFloat(opt.dataset.price).toFixed(2);
        }
        if (opt.dataset.gst !== undefined && opt.dataset.gst !== '') {
            row.querySelector('.gst-input').value = parseFloat(opt.dataset.gst);
        }
        recalcRow(row);
    }

    function attachRow(row) {
        row.querySelector('.product-select').addEventListener('change', () => onProductSelect(row));
        row.querySelector('.qty-input').addEventListener('input', () => recalcRow(row));
        row.querySelector('.rate-input').addEventListener('input', () => recalcRow(row));
        row.querySelector('.gst-input').addEventListener('input', () => recalcRow(row));
        row.querySelector('.remove-row').addEventListener('click', function () {
            if (document.querySelectorAll('.product-row').length > 1) {
                row.remove();
                recalcTotals();
            } else {
                alert('At least one product row is required.');
            }
        });
        recalcRow(row);
    }

    function addRow() {
        const first = document.querySelector('.product-row');
        const clone = first.cloneNode(true);
        // Reset the cloned row's values
        clone.querySelector('.product-select').innerHTML = optionsHtml;
        clone.querySelector('.product-select').selectedIndex = 0;
        clone.querySelector('.product-id').value = '';
        clone.querySelector('.product-name').value = '';
        clone.querySelector('.hsn-input').value = '';
        clone.querySelector('.qty-input').value = 1;
        clone.querySelector('.unit-input').value = '';
        clone.querySelector('.rate-input').value = '';
        clone.querySelector('.gst-input').value = 0;
        clone.querySelector('.row-amount').value = '';
        productRows.appendChild(clone);
        attachRow(clone);
    }

    addRowBtn.addEventListener('click', addRow);
    interstateToggle.addEventListener('change', recalcTotals);

    // Wire up the initial row(s)
    document.querySelectorAll('.product-row').forEach(attachRow);
    recalcTotals();
})();
</script>
@endsection
