<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;
    use ActivityTrait;

    protected static $logName = 'Purchase Return';

    public function getLogDescription(string $event): string
    {
        return "Purchase Return for <strong>{$this->product->product_name}</strong> (Qty: {$this->quantity}) has been {$event} by";
    }

    protected static $logAttributes = ['purchase_id', 'product_id', 'return_date', 'quantity', 'return_price', 'total_return_amount', 'reason'];

    protected $fillable = [
        'purchase_id',
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

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
