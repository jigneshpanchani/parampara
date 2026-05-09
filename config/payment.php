<?php

/*
|--------------------------------------------------------------------------
| Payment Modes & Methods
|--------------------------------------------------------------------------
|
| Single source of truth for all payment-related dropdowns, validation
| rules, and label lookups. Keys MUST match the DB enum values exactly.
| Values are the user-facing labels.
|
| Helpers:
|   config('payment.sale_modes')              => ['cash' => 'Cash', ...]
|   array_keys(config('payment.sale_modes'))  => ['cash', 'upi', ...]
|
*/

return [

    /*
     * Sale.payment_mode (DB enum: cash, upi, gpay, mix)
     * Used when recording a sale.
     */
    'sale_modes' => [
        'cash' => 'Cash',
        'upi'  => 'UPI',
        'gpay' => 'G-Pay',
        'mix'  => 'Mix (Cash & Online)',
    ],

    /*
     * SalePayment.payment_method (DB enum: cash, upi, gpay, bank_transfer, cheque, other)
     * Used when adding a follow-up payment to an existing sale.
     */
    'sale_payment_methods' => [
        'cash'          => 'Cash',
        'upi'           => 'UPI',
        'gpay'          => 'G-Pay',
        'bank_transfer' => 'Bank Transfer',
        'cheque'        => 'Cheque',
        'other'         => 'Other',
    ],

    /*
     * Expense.payment_method (DB enum: Cash, G-Pay, Online Transfer)
     * NOTE: keys are MixedCase to match the existing DB enum values.
     */
    'expense_methods' => [
        'Cash'            => 'Cash',
        'G-Pay'           => 'G-Pay',
        'Online Transfer' => 'Online Transfer',
    ],

    /*
     * Payment.payment_method for purchase payments
     * (DB enum: cash, cheque, bank_transfer, credit_card, other)
     */
    'purchase_payment_methods' => [
        'cash'          => 'Cash',
        'cheque'        => 'Cheque',
        'bank_transfer' => 'Bank Transfer',
        'credit_card'   => 'Credit Card',
        'other'         => 'Other',
    ],

    /*
     * Subset of sale_modes considered "online" for filter/aggregation purposes.
     */
    'sale_modes_online' => ['upi', 'gpay'],

];
