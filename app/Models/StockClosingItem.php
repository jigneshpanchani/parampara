<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockClosingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_closing_id',
        'product_id',
        'opening_stock',
        'purchased_qty',
        'purchase_returns_qty',
        'sold_qty',
        'sale_returns_qty',
        'expected_stock',
        'actual_stock',
        'difference',
    ];

    protected $casts = [
        'opening_stock'        => 'float',
        'purchased_qty'        => 'float',
        'purchase_returns_qty' => 'float',
        'sold_qty'             => 'float',
        'sale_returns_qty'     => 'float',
        'expected_stock'       => 'float',
        'actual_stock'         => 'float',
        'difference'           => 'float',
    ];

    public function stockClosing()
    {
        return $this->belongsTo(StockClosing::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
