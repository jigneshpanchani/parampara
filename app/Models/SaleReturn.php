<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;
    use ActivityTrait;

    protected static $logName = 'Sale Return';

    public function getLogDescription(string $event): string
    {
        return "Sale Return for <strong>{$this->product->product_name}</strong> (Qty: {$this->quantity}) has been {$event} by";
    }

    protected static $logAttributes = ['sale_id', 'product_id', 'return_date', 'quantity', 'return_price', 'total_return_amount', 'reason'];

    protected $fillable = [
        'sale_id',
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

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
