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
     * Get remaining amount to be paid
     */
    public function getRemainingAmount()
    {
        return $this->total_amount - $this->getTotalPaidAmount();
    }

    /**
     * Check if purchase is fully paid
     */
    public function isFullyPaid()
    {
        return $this->getRemainingAmount() <= 0;
    }
}
