<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellReturn extends Model
{
    use HasFactory;
    use ActivityTrait;

    protected static $logName = 'Sell Return';

    public function getLogDescription(string $event): string
    {
        return "Sell Return for <strong>{$this->product->product_name}</strong> (Qty: {$this->quantity}) has been {$event} by";
    }

    protected static $logAttributes = ['sell_id', 'product_id', 'return_date', 'quantity', 'return_price', 'total_return_amount', 'reason'];

    protected $fillable = [
        'sell_id',
        'product_id',
        'return_date',
        'quantity',
        'return_price',
        'total_return_amount',
        'reason',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
