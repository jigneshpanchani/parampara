<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_date',
        'seller_name',
        'seller_contact_number',
        'total_amount',
        'payment_mode',
        'payment_status',
        'amount_paid',
        'cash_amount',
        'online_amount',
        'pending_amount',
        'notes',
    ];

    protected $casts = [
        'sell_date' => 'date',
        'total_amount' => 'float',
        'amount_paid' => 'float',
        'cash_amount' => 'float',
        'online_amount' => 'float',
        'pending_amount' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(SellItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SellReturn::class);
    }

    public function sellPayments()
    {
        return $this->hasMany(SellPayment::class);
    }

    public function getTotalPaidFromPayments(): float
    {
        return (float) $this->sellPayments()->sum('amount');
    }

    /**
     * Total ever paid = initial amount_paid + all subsequent sell_payments.
     * Uses pending_amount as the source of truth since it is kept accurate
     * by recalculatePaymentStatus() after every payment.
     */
    public function getTotalPaidAttribute(): float
    {
        return max(0, $this->total_amount - $this->pending_amount);
    }

    public function getRemainingAmount(): float
    {
        return (float) $this->pending_amount;
    }

    public function recalculatePaymentStatus(): void
    {
        $payments = $this->sellPayments()->get(['amount', 'payment_method']);
        $totalPaidFromPayments = (float) $payments->sum('amount');
        $totalPaid = $this->amount_paid + $totalPaidFromPayments;

        if ($totalPaid <= 0) {
            $status = 'pending';
        } elseif ($totalPaid >= $this->total_amount) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }

        $updates = [
            'payment_status' => $status,
            'pending_amount' => max(0, $this->total_amount - $totalPaid),
        ];

        // Auto-fill payment_mode from sell_payments when it was not set at sale time
        if ($this->payment_mode === null && $payments->isNotEmpty()) {
            $methods = $payments->pluck('payment_method')->unique()->values();
            if ($methods->count() === 1 && in_array($methods[0], ['cash', 'upi', 'gpay'])) {
                $updates['payment_mode'] = $methods[0];
            } elseif ($methods->count() > 1) {
                $updates['payment_mode'] = 'mix';
            }
        }

        $this->update($updates);
    }

    public function cashSellInvoice()
    {
        return $this->belongsTo(SellInvoice::class, 'cash_sell_invoice_id');
    }

    public function onlineSellInvoice()
    {
        return $this->belongsTo(SellInvoice::class, 'online_sell_invoice_id');
    }

    public function mixSellInvoice()
    {
        return $this->belongsTo(SellInvoice::class, 'mix_sell_invoice_id');
    }

    /**
     * Get formatted total amount.
     */
    public function getTotalAmountFormattedAttribute(): string
    {
        return '₹' . number_format($this->total_amount, 2);
    }

    /**
     * Get payment mode label for display.
     */
    public function getPaymentModeLabelAttribute(): string
    {
        return match ($this->payment_mode) {
            'gpay' => 'G-Pay',
            'cash' => 'Cash',
            'upi' => 'UPI',
            'mix' => 'Mix',
            default => $this->payment_mode ? strtoupper($this->payment_mode) : '—',
        };
    }

}
