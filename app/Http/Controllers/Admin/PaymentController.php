<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        //$this->middleware('permission:List Payment')->only(['index']);
        //$this->middleware('permission:Create Payment')->only(['create', 'store']);
        //$this->middleware('permission:Edit Payment')->only(['edit', 'update']);
        //$this->middleware('permission:Delete Payment')->only(['destroy']);
    }

    /**
     * Display a listing of payments
     */
    public function index()
    {
        $payments = Payment::with('purchase')->latest()->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        $purchases = Purchase::where('status', '!=', 'cancelled')->latest()->get();
        return view('admin.payments.create', compact('purchases'));
    }

    /**
     * Store a newly created payment in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,credit_card,other',
            'payment_status' => 'required|in:pending,paid,failed,cancelled',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create($validated);

        return redirect()->route('admin.payments.index')->with('success', 'Payment created successfully.');
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load('purchase');
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(Payment $payment)
    {
        $purchases = Purchase::where('status', '!=', 'cancelled')->latest()->get();
        return view('admin.payments.edit', compact('payment', 'purchases'));
    }

    /**
     * Update the specified payment in storage
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,credit_card,other',
            'payment_status' => 'required|in:pending,paid,failed,cancelled',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('admin.payments.index')->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment from storage
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully.');
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(Payment $payment)
    {
        $payment->markAsPaid();
        return redirect()->back()->with('success', 'Payment marked as paid.');
    }

    /**
     * Get payments for a specific purchase
     */
    public function getPaymentsByPurchase($purchaseId)
    {
        $payments = Payment::where('purchase_id', $purchaseId)->get();
        return response()->json($payments);
    }
}

