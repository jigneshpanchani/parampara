<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'closing_date',
        'period_label',
        'notes',
    ];

    protected $casts = [
        'closing_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(StockClosingItem::class);
    }
}
