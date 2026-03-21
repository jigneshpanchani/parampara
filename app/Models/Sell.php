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

    public function cashSellInvoice()
    {
        return $this->belongsTo(SellInvoice::class, 'cash_sell_invoice_id');
    }

    public function onlineSellInvoice()
    {
        return $this->belongsTo(SellInvoice::class, 'online_sell_invoice_id');
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
            default => strtoupper($this->payment_mode ?? ''),
        };
    }

}
