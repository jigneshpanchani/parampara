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
        'is_pay_later',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'float',
        'amount_paid' => 'float',
        'cash_amount' => 'float',
        'online_amount' => 'float',
        'pending_amount' => 'float',
        'is_pay_later' => 'boolean',
    ];

    /**
     * Sales that were recorded with an unpaid balance at sale time.
     * Stays true even after the customer clears the balance later (flag is set at
     * sale entry by SaleController::store and intentionally not touched on edits).
     */
    public function scopePayLater($query)
    {
        return $query->where('is_pay_later', true);
    }

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

        // Keep the sales table in sync with the follow-up payments for pay-later
        // sales (recorded with nothing paid up front). This is what turns a "pay
        // later" sale into Mix once it is settled partly in cash and partly online,
        // and fills the cash_amount / online_amount split that the list, detail and
        // edit screens read. Derived purely from sale_payments, so it is idempotent.
        // Direct sales (amount_paid > 0) keep the mode/split entered at sale time.
        if ($payments->isNotEmpty() && abs((float) $this->amount_paid) < 0.01) {
            $cash   = (float) $payments->where('payment_method', 'cash')->sum('amount');
            $online = (float) $payments->where('payment_method', '!=', 'cash')->sum('amount');

            if ($cash > 0 && $online > 0) {
                $updates['payment_mode']  = 'mix';
                $updates['cash_amount']   = $cash;
                $updates['online_amount'] = $online;
            } elseif ($cash > 0) {
                $updates['payment_mode']  = 'cash';
                $updates['cash_amount']   = 0;
                $updates['online_amount'] = 0;
            } else { // online only
                $methods = $payments->pluck('payment_method')->unique()->values();
                $updates['payment_mode']  = $methods->count() === 1 ? $methods[0] : 'mix';
                $updates['cash_amount']   = 0;
                $updates['online_amount'] = $methods->count() === 1 ? 0 : $online;
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
        if (! $this->payment_mode) {
            return '—';
        }

        return config("payment.sale_modes.{$this->payment_mode}", strtoupper($this->payment_mode));
    }
}
