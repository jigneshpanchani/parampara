<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_invoice_id',
        'product_id',
        'product_name',
        'hsn_code',
        'unit_of_measure',
        'quantity',
        'rate',
        'gst_rate',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'float',
        'rate' => 'float',
        'gst_rate' => 'float',
        'taxable_amount' => 'float',
        'cgst_amount' => 'float',
        'sgst_amount' => 'float',
        'igst_amount' => 'float',
        'line_total' => 'float',
    ];

    public function taxInvoice(): BelongsTo
    {
        return $this->belongsTo(TaxInvoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
