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
        'pending_amount',
        'notes',
    ];

    protected $casts = [
        'sell_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(SellItem::class);
    }

    public function returns()
    {
        return $this->hasMany(SellReturn::class);
    }

}
