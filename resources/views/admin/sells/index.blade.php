@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
@if ($message = session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ $message }}</div>
@endif
@if ($message = session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">{{ $message }}</div>
@endif
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">💰 Sales</h2>
    <a href="{{ route('admin.sells.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        + Record New Sale
    </a>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" action="{{ route('admin.sells.index') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From date</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To date</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
        </div>
        <div>
            <label for="payment_mode" class="block text-sm font-medium text-gray-700 mb-1">Payment mode</label>
            <select name="payment_mode" id="payment_mode" class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm min-w-[140px]">
                <option value="">All</option>
                <option value="cash" {{ request('payment_mode') === 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="upi" {{ request('payment_mode') === 'upi' ? 'selected' : '' }}>UPI</option>
                <option value="gpay" {{ request('payment_mode') === 'gpay' ? 'selected' : '' }}>G-Pay</option>
                <option value="mix" {{ request('payment_mode') === 'mix' ? 'selected' : '' }}>Mix</option>
            </select>
        </div>
        <div>
            <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment status</label>
            <select name="payment_status" id="payment_status" class="rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm min-w-[130px]">
                <option value="">All</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">Filter</button>
            <a href="{{ route('admin.sells.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm inline-flex items-center">Clear</a>
        </div>
    </form>
</div>

@if ($sells->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No sales found. <a href="{{ route('admin.sells.create') }}" class="text-blue-500 hover:underline">Record one now</a></p>
    </div>
@else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">#</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Seller Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Paid Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Mode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Pending</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Notes</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Invoices</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sells as $index => $sell)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $sells->firstItem() + $index }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sell->sell_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="font-medium">{{ $sell->seller_name ?? '-' }}</div>
                            @if($sell->seller_contact_number)
                                <div class="text-xs text-gray-400">{{ $sell->seller_contact_number }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($sell->total_amount, 2, '.', '') }}</td>
                        <td class="px-6 py-4 text-sm text-green-600 font-semibold">
                            ₹{{ number_format($sell->total_paid, 2, '.', '') }}
                            @if($sell->payment_mode === 'mix' && ($sell->cash_amount > 0 || $sell->online_amount > 0))
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="text-blue-600">Cash: ₹{{ number_format($sell->cash_amount, 2, '.', '') }}</span> |
                                    <span class="text-purple-600">Online: ₹{{ number_format($sell->online_amount, 2, '.', '') }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($sell->payment_mode)
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_mode === 'cash' ? 'bg-blue-100 text-blue-800' : ($sell->payment_mode === 'upi' ? 'bg-purple-100 text-purple-800' : ($sell->payment_mode === 'gpay' ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800')) }}">
                                    {{ $sell->payment_mode_label }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $sell->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($sell->payment_status === 'pending' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($sell->payment_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-red-600">₹{{ number_format($sell->pending_amount, 2, '.', '') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($sell->notes)
                                <span class="text-xs" title="{{ $sell->notes }}">{{ Str::limit($sell->notes, 30) }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex flex-col gap-1 items-center text-xs">
                                @if($sell->cashSellInvoice)
                                    <a href="{{ route('admin.sell-invoices.show', $sell->cashSellInvoice) }}" class="font-mono text-blue-700 hover:underline" title="Cash invoice">{{ $sell->cashSellInvoice->invoice_number }}</a>
                                @endif
                                @if($sell->onlineSellInvoice)
                                    <a href="{{ route('admin.sell-invoices.show', $sell->onlineSellInvoice) }}" class="font-mono text-purple-700 hover:underline" title="Online invoice">{{ $sell->onlineSellInvoice->invoice_number }}</a>
                                @endif
                                @if($sell->mixSellInvoice)
                                    <a href="{{ route('admin.sell-invoices.show', $sell->mixSellInvoice) }}" class="font-mono text-indigo-700 hover:underline" title="Mix invoice">{{ $sell->mixSellInvoice->invoice_number }}</a>
                                @endif
                                @if(!$sell->cashSellInvoice && !$sell->onlineSellInvoice && !$sell->mixSellInvoice)
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm flex gap-3 justify-center items-center">
                            @if(in_array($sell->payment_status, ['pending', 'partial']))
                                <button type="button" onclick="openSellPaymentModal({{ $sell->id }})"
                                    class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded transition whitespace-nowrap"
                                    title="Record / view payments">
                                    Pay
                                </button>
                            @endif
                            <a href="{{ route('admin.sells.show', $sell) }}" class="text-green-500 hover:text-green-700 transition" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('admin.sells.edit', $sell) }}" class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.sells.destroy', $sell) }}" method="POST" class="inline delete-form" data-item-name="Sale #{{ $sell->id }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-6">
        {{ $sells->links() }}
    </div>
@endif

<!-- Sell Payment Details Modal -->
<div id="sellPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 overflow-y-auto pt-4">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-800">Payment Details</h3>
            <button type="button" onclick="closeSellPaymentModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Sell Summary -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap gap-6 items-center">
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Seller</p>
                    <p id="spSellerName" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Sell Date</p>
                    <p id="spSellDate" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Payment Mode</p>
                    <p id="spPaymentMode" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Status</p>
                    <p id="spPaymentStatus" class="text-sm font-semibold text-gray-800"></p>
                </div>
            </div>
        </div>

        <!-- Sell Items -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Sell Items</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Product</th>
                            <th class="px-4 py-2 text-right">Quantity</th>
                            <th class="px-4 py-2 text-right">Price</th>
                            <th class="px-4 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody id="spItemsTable"></tbody>
                </table>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="border-r pr-4">
                    <p class="text-gray-600 text-xs font-semibold uppercase mb-2">Amount Breakdown</p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Amount:</span>
                            <span id="spTotalAmount" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Initial Paid:</span>
                            <span id="spInitialPaid" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Payments Total:</span>
                            <span id="spPaymentsTotal" class="font-semibold text-gray-800"></span>
                        </div>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-600 text-xs font-semibold uppercase mb-2">Payment Status</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Paid:</span>
                            <span id="spTotalPaid" class="font-semibold text-green-600"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Remaining:</span>
                            <span id="spRemaining" class="font-semibold text-red-600"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Payment History</h4>
            <div id="spPaymentHistory" class="space-y-2 max-h-48 overflow-y-auto"></div>
        </div>

        <!-- Add Payment Form -->
        <div id="spAddPaymentSection" class="border-t pt-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Add New Payment</h4>
            <form id="spAddPaymentForm" onsubmit="addSellPayment(event)">
                @csrf
                <div class="grid grid-cols-4 gap-3 mb-3">
                    <div>
                        <label for="spPaymentDate" class="block text-xs font-semibold text-gray-700 mb-1">Payment Date *</label>
                        <input type="date" id="spPaymentDate" name="payment_date" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="spPaymentAmount" class="block text-xs font-semibold text-gray-700 mb-1">Amount *</label>
                        <input type="number" id="spPaymentAmount" name="amount" step="0.01" min="0.01" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="spPaymentMethod" class="block text-xs font-semibold text-gray-700 mb-1">Method *</label>
                        <select id="spPaymentMethod" name="payment_method" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="gpay">G-Pay</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="spReferenceNumber" class="block text-xs font-semibold text-gray-700 mb-1">Reference</label>
                        <input type="text" id="spReferenceNumber" name="reference_number" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Txn ID">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="spPaymentNotes" class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea id="spPaymentNotes" name="notes" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="2" placeholder="Additional details..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeSellPaymentModal()" class="px-3 py-1 text-sm bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">Close</button>
                    <button type="submit" class="px-3 py-1 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentSellId = null;

function openSellPaymentModal(sellId) {
    currentSellId = sellId;
    document.getElementById('sellPaymentModal').classList.remove('hidden');
    loadSellPaymentDetails(sellId);
}

function closeSellPaymentModal() {
    document.getElementById('sellPaymentModal').classList.add('hidden');
    currentSellId = null;
}

function loadSellPaymentDetails(sellId) {
    fetch(`/admin/sells/${sellId}/payment-details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const sell = data.sell;

                document.getElementById('spSellerName').textContent = sell.seller_name;
                document.getElementById('spSellDate').textContent = sell.sell_date;
                document.getElementById('spPaymentMode').textContent = sell.payment_mode;
                document.getElementById('spPaymentStatus').textContent = sell.payment_status;

                document.getElementById('spTotalAmount').textContent = '₹' + sell.total_amount;
                document.getElementById('spInitialPaid').textContent = '₹' + sell.initial_paid;
                document.getElementById('spPaymentsTotal').textContent = '₹' + sell.payments_total;
                document.getElementById('spTotalPaid').textContent = '₹' + sell.total_paid;
                document.getElementById('spRemaining').textContent = '₹' + sell.remaining_amount;

                // Populate items
                const itemsTable = document.getElementById('spItemsTable');
                itemsTable.innerHTML = '';
                data.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="px-4 py-2">${item.product_name}</td>
                        <td class="px-4 py-2 text-right">${item.quantity}</td>
                        <td class="px-4 py-2 text-right">₹${item.selling_price}</td>
                        <td class="px-4 py-2 text-right">₹${item.total_price}</td>
                    `;
                    itemsTable.appendChild(row);
                });

                // Payment history
                const paymentHistory = document.getElementById('spPaymentHistory');
                paymentHistory.innerHTML = '';
                if (data.payments.length === 0) {
                    paymentHistory.innerHTML = '<p class="text-gray-600 text-center py-4">No payments recorded yet</p>';
                } else {
                    data.payments.forEach(payment => {
                        const div = document.createElement('div');
                        div.className = 'bg-gray-50 rounded-lg p-2 border-l-4 border-green-500 text-sm';
                        let refText = payment.reference_number !== '-' ? ` • Ref: ${payment.reference_number}` : '';
                        let notesText = payment.notes !== '-' ? ` • ${payment.notes}` : '';
                        div.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">₹${payment.amount} • ${payment.payment_method} • ${payment.payment_date}${refText}${notesText}</p>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded ml-2 whitespace-nowrap">Paid</span>
                            </div>
                        `;
                        paymentHistory.appendChild(div);
                    });
                }

                // Set max amount and toggle form visibility
                const remaining = parseFloat(sell.remaining_amount.replace(/,/g, ''));
                if (remaining <= 0) {
                    document.getElementById('spAddPaymentSection').classList.add('hidden');
                } else {
                    document.getElementById('spAddPaymentSection').classList.remove('hidden');
                    document.getElementById('spPaymentAmount').max = remaining;
                    document.getElementById('spPaymentAmount').placeholder = `Max: ₹${sell.remaining_amount}`;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showSellPaymentNotification('Failed to load payment details', 'error');
        });
}

function addSellPayment(event) {
    event.preventDefault();

    const formData = new FormData(document.getElementById('spAddPaymentForm'));
    fetch(`/admin/sells/${currentSellId}/add-payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('#spAddPaymentForm input[name="_token"]').value,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSellPaymentNotification('Payment recorded successfully!', 'success');
            document.getElementById('spAddPaymentForm').reset();
            document.getElementById('spPaymentDate').value = new Date().toISOString().split('T')[0];
            loadSellPaymentDetails(currentSellId);
            setTimeout(() => { location.reload(); }, 2000);
        } else {
            showSellPaymentNotification(data.message || 'Failed to record payment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showSellPaymentNotification('An error occurred while recording payment', 'error');
    });
}

function showSellPaymentNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-[60] ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => { notification.remove(); }, 3000);
}

document.getElementById('sellPaymentModal')?.addEventListener('click', function(event) {
    if (event.target === this) closeSellPaymentModal();
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeSellPaymentModal();
});
</script>
@endsection

