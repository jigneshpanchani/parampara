<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_date',
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
        'sale_date' => 'date',
        'total_amount' => 'float',
        'amount_paid' => 'float',
        'cash_amount' => 'float',
        'online_amount' => 'float',
        'pending_amount' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function getTotalPaidFromPayments(): float
    {
        return (float) $this->salePayments()->sum('amount');
    }

    /**
     * Total ever paid = initial amount_paid + all subsequent sale_payments.
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
        $payments = $this->salePayments()->get(['amount', 'payment_method']);
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

        // Auto-fill payment_mode from sale_payments when it was not set at sale time
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

    public function cashSaleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'cash_sale_invoice_id');
    }

    public function onlineSaleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'online_sale_invoice_id');
    }

    public function mixSaleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class, 'mix_sale_invoice_id');
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
