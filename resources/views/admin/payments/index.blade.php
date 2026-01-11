@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">💳 Payments</h1>
            <p class="text-gray-600 mt-2">Manage purchase payments</p>
        </div>
        <a href="{{ route('admin.payments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
            + New Payment
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Purchase ID</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Payment Date</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Amount</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Method</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">#{{ $payment->purchase_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->payment_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-800">₹{{ number_format($payment->amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $payment->getPaymentMethodLabel() }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @if($payment->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($payment->payment_status === 'failed') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($payment->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View</a>
                            <a href="{{ route('admin.payments.edit', $payment) }}" class="text-green-600 hover:text-green-800 text-sm font-semibold">Edit</a>
                            @if($payment->payment_status !== 'paid')
                            <form action="{{ route('admin.payments.mark-as-paid', $payment) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-purple-600 hover:text-purple-800 text-sm font-semibold">Mark Paid</button>
                            </form>
                            @endif
                            <form action="{{ route('admin.payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-semibold">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No payments found. <a href="{{ route('admin.payments.create') }}" class="text-blue-600 hover:underline">Create one</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $payments->links() }}
    </div>
</div>
@endsection

