<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ActivityTrait;

class Payment extends Model
{
    use HasFactory;
    use ActivityTrait;

    protected static $logName = 'Payment';

    public function getLogDescription(string $event): string
    {
        return "Payment of ₹{$this->amount} for Purchase #{$this->purchase_id} has been {$event} by";
    }

    protected static $logAttributes = ['purchase_id', 'payment_date', 'amount', 'payment_method', 'payment_status', 'reference_number'];

    protected $fillable = [
        'purchase_id',
        'payment_date',
        'amount',
        'payment_method',
        'payment_status',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the purchase associated with this payment
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Check if payment is paid
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid()
    {
        $this->update(['payment_status' => 'paid']);
        return $this;
    }

    /**
     * Mark payment as pending
     */
    public function markAsPending()
    {
        $this->update(['payment_status' => 'pending']);
        return $this;
    }

    /**
     * Get payment status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->payment_status) {
            'paid' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabel()
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'cheque' => 'Cheque',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'other' => 'Other',
            default => 'Unknown',
        };
    }
}

