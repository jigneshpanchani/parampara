<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_date',
        'supplier_name',
        'bill_details',
        'transportation_cost',
        'bill_due_date',
        'total_amount',
        'status',
        'expense',
        'expense_details',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'bill_due_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if purchase has any paid payment
     */
    public function hasPaidPayment()
    {
        return $this->payments()->where('payment_status', 'paid')->exists();
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAmount()
    {
        return $this->payments()
            ->where('payment_status', 'paid')
            ->sum('amount');
    }

    /**
     * Get subtotal (items only)
     */
    public function getSubtotal()
    {
        return $this->items()->sum('total_price');
    }

    /**
     * Get total payable amount (items + transportation + expense)
     */
    public function getTotalPayableAmount()
    {
        $subtotal = $this->getSubtotal();
        $transportation = $this->transportation_cost ?? 0;
        $expense = $this->expense ?? 0;
        return $subtotal + $transportation + $expense;
    }

    /**
     * Get remaining amount to be paid
     */
    public function getRemainingAmount()
    {
        return $this->getTotalPayableAmount() - $this->getTotalPaidAmount();
    }

    /**
     * Check if purchase is fully paid
     */
    public function isFullyPaid()
    {
        return $this->getRemainingAmount() <= 0;
    }

    public function getPaymentStatus()
    {
        $totalPaid = $this->getTotalPaidAmount();
        if ($totalPaid <= 0) {
            return 'pending';
        } elseif ($totalPaid >= $this->total_amount) {
            return 'paid';
        } else {
            return 'partial';
        }
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusColor()
    {
        return match($this->getPaymentStatus()) {
            'paid' => 'green',
            'partial' => 'blue',
            'pending' => 'yellow',
            default => 'gray',
        };
    }
}
