@extends('layouts.admin')

@section('title', 'Purchases')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">🛒 Purchases</h2>
    <a href="{{ route('admin.purchases.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
        + Add New Purchase
    </a>
</div>

<!-- Dashboard Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Purchases</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">₹{{ number_format($totalPurchases, 2) }}</p>
            </div>
            <div class="text-4xl text-blue-500">📊</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Paid</p>
                <p class="text-3xl font-bold text-green-600 mt-2">₹{{ number_format($totalPaid, 2) }}</p>
            </div>
            <div class="text-4xl text-green-500">✅</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold">Total Pending</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2">₹{{ number_format($totalPending, 2) }}</p>
            </div>
            <div class="text-4xl text-yellow-500">⏳</div>
        </div>
    </div>
</div>

@if ($purchases->isEmpty())
    <div class="bg-white rounded-lg shadow p-6 text-center">
        <p class="text-gray-600">No purchases found. <a href="{{ route('admin.purchases.create') }}" class="text-blue-500 hover:underline">Create one now</a></p>
    </div>
@else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Due Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchases as $purchase)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $purchase->purchase_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->supplier_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->items->count() }} items</td>
                        <td class="px-6 py-4 text-sm text-gray-600">₹{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->bill_due_date ? $purchase->bill_due_date->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @php
                                $paymentStatus = $purchase->getPaymentStatus();
                                $statusColor = $purchase->getPaymentStatusColor();
                                $colorMap = [
                                    'green' => 'bg-green-100 text-green-800',
                                    'blue' => 'bg-blue-100 text-blue-800',
                                    'yellow' => 'bg-yellow-100 text-yellow-800',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colorMap[$statusColor] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($paymentStatus) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-3 flex items-center">
                            <button type="button" onclick="openPaymentModal({{ $purchase->id }})" class="text-green-500 hover:text-green-700 text-xl transition" title="Payment Details">
                                ₹
                            </button>
                            <a href="{{ route('admin.purchases.edit', $purchase) }}" class="text-blue-500 hover:text-blue-700 text-xl transition" title="Edit Purchase">
                                ✏️
                            </a>
                            <form action="{{ route('admin.purchases.destroy', $purchase) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xl transition" title="Delete Purchase">
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

<!-- Payment Details Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 overflow-y-auto pt-4">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-4xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-2xl font-bold text-gray-800">Payment Details</h3>
            <button type="button" onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Purchase Summary -->
        <div id="purchaseSummary" class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex flex-wrap gap-6 items-center">
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Supplier</p>
                    <p id="supplierName" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Purchase Date</p>
                    <p id="purchaseDate" class="text-sm font-semibold text-gray-800"></p>
                </div>
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Total Payable</p>
                    <p id="totalAmount" class="text-sm font-semibold text-blue-600"></p>
                </div>
                <div class="flex-1 min-w-max">
                    <p class="text-gray-600 text-xs font-semibold uppercase">Status</p>
                    <p id="paymentStatus" class="text-sm font-semibold text-gray-800"></p>
                </div>
            </div>
        </div>

        <!-- Purchase Items -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Purchase Items</h4>
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
                    <tbody id="itemsTable">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="border-r pr-4">
                    <p class="text-gray-600 text-xs font-semibold uppercase mb-2">Cost Breakdown</p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Subtotal (Items):</span>
                            <span id="summarySubtotal" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Transportation:</span>
                            <span id="summaryTransportation" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Expense:</span>
                            <span id="summaryExpense" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="border-t pt-1 mt-1 flex justify-between font-bold">
                            <span class="text-gray-800">Total Payable:</span>
                            <span id="summaryTotalAmount" class="text-lg text-blue-600"></span>
                        </div>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-600 text-xs font-semibold uppercase mb-2">Payment Status</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Total Paid:</span>
                            <span id="summaryTotalPaid" class="font-semibold text-green-600"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Remaining:</span>
                            <span id="summaryRemaining" class="font-semibold text-yellow-600"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Payment History</h4>
            <div id="paymentHistory" class="space-y-2 max-h-48 overflow-y-auto">
            </div>
        </div>

        <!-- Add Payment Form -->
        <div class="border-t pt-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Add New Payment</h4>
            <form id="addPaymentForm" onsubmit="addPayment(event)">
                @csrf
                <div class="grid grid-cols-4 gap-3 mb-3">
                    <div>
                        <label for="paymentDate" class="block text-xs font-semibold text-gray-700 mb-1">Payment Date *</label>
                        <input type="date" id="paymentDate" name="payment_date" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label for="paymentAmount" class="block text-xs font-semibold text-gray-700 mb-1">Amount *</label>
                        <input type="number" id="paymentAmount" name="amount" step="0.01" min="0.01" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label for="paymentMethod" class="block text-xs font-semibold text-gray-700 mb-1">Method *</label>
                        <select id="paymentMethod" name="payment_method" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="referenceNumber" class="block text-xs font-semibold text-gray-700 mb-1">Reference</label>
                        <input type="text" id="referenceNumber" name="reference_number" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Cheque/Txn ID">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="paymentNotes" class="block text-xs font-semibold text-gray-700 mb-1">Notes</label>
                    <textarea id="paymentNotes" name="notes" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" rows="3" placeholder="Additional details..."></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closePaymentModal()" class="px-3 py-1 text-sm bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                        Close
                    </button>
                    <button type="submit" class="px-3 py-1 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentPurchaseId = null;

function openPaymentModal(purchaseId) {
    currentPurchaseId = purchaseId;
    document.getElementById('paymentModal').classList.remove('hidden');
    loadPaymentDetails(purchaseId);
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
    currentPurchaseId = null;
}

function loadPaymentDetails(purchaseId) {
    const url = `/admin/purchases/${purchaseId}/payment-details`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const purchase = data.purchase;

                // Update purchase summary
                document.getElementById('supplierName').textContent = purchase.supplier_name;
                document.getElementById('purchaseDate').textContent = purchase.purchase_date;
                document.getElementById('totalAmount').textContent = '₹' + purchase.total_payable;
                document.getElementById('paymentStatus').textContent = purchase.payment_status;

                // Update payment summary with cost breakdown
                document.getElementById('summarySubtotal').textContent = '₹' + purchase.subtotal;
                document.getElementById('summaryTransportation').textContent = '₹' + purchase.transportation_cost;
                document.getElementById('summaryExpense').textContent = '₹' + purchase.expense;
                document.getElementById('summaryTotalAmount').textContent = '₹' + purchase.total_payable;
                document.getElementById('summaryTotalPaid').textContent = '₹' + purchase.total_paid;
                document.getElementById('summaryRemaining').textContent = '₹' + purchase.remaining_amount;

                // Populate items table
                const itemsTable = document.getElementById('itemsTable');
                itemsTable.innerHTML = '';
                data.items.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'border-b';
                    row.innerHTML = `
                        <td class="px-4 py-2">${item.product_name}</td>
                        <td class="px-4 py-2 text-right">${item.quantity}</td>
                        <td class="px-4 py-2 text-right">₹${item.purchase_price}</td>
                        <td class="px-4 py-2 text-right">₹${item.total_price}</td>
                    `;
                    itemsTable.appendChild(row);
                });

                // Populate payment history
                const paymentHistory = document.getElementById('paymentHistory');
                paymentHistory.innerHTML = '';
                if (data.payments.length === 0) {
                    paymentHistory.innerHTML = '<p class="text-gray-600 text-center py-4">No payments recorded yet</p>';
                } else {
                    data.payments.forEach(payment => {
                        const paymentDiv = document.createElement('div');
                        paymentDiv.className = 'bg-gray-50 rounded-lg p-2 border-l-4 border-green-500 text-sm';
                        let refText = payment.reference_number !== '-' ? ` • Ref: ${payment.reference_number}` : '';
                        let notesText = payment.notes !== '-' ? ` • ${payment.notes}` : '';
                        paymentDiv.innerHTML = `
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">₹${payment.amount} • ${payment.payment_method} • ${payment.payment_date}${refText}${notesText}</p>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded ml-2 whitespace-nowrap">${payment.payment_status}</span>
                            </div>
                        `;
                        paymentHistory.appendChild(paymentDiv);
                    });
                }

                // Set remaining amount in form
                const remainingAmount = parseFloat(purchase.remaining_amount.replace(/,/g, ''));
                document.getElementById('paymentAmount').max = remainingAmount;
                document.getElementById('paymentAmount').placeholder = `Max: ₹${purchase.remaining_amount}`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to load payment details', 'error');
        });
}

function addPayment(event) {
    event.preventDefault();

    const formData = new FormData(document.getElementById('addPaymentForm'));
    const url = `/admin/purchases/${currentPurchaseId}/add-payment`;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Payment recorded successfully!', 'success');
            document.getElementById('addPaymentForm').reset();
            document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
            loadPaymentDetails(currentPurchaseId);

            // Reload page after 2 seconds to update dashboard
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showNotification('Failed to record payment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while recording payment', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('paymentModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        closePaymentModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closePaymentModal();
    }
});
</script>
@endsection

