<?php

namespace App\Models;

use App\Support\IndianNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'buyer_name',
        'buyer_address',
        'buyer_gstin',
        'buyer_state',
        'buyer_contact_number',
        'vehicle_no',
        'transport',
        'broker',
        'eway_bill_no',
        'is_interstate',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'grand_total',
        'amount_in_words',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'is_interstate' => 'boolean',
        'taxable_amount' => 'float',
        'cgst_amount' => 'float',
        'sgst_amount' => 'float',
        'igst_amount' => 'float',
        'grand_total' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(TaxInvoiceItem::class);
    }

    /**
     * Compute one line's taxable value + tax split from qty, rate and GST %.
     * Intra-state → CGST + SGST (half each); inter-state → IGST (full rate).
     *
     * @return array{taxable_amount: float, cgst_amount: float, sgst_amount: float, igst_amount: float, line_total: float}
     */
    public static function computeLine(float $quantity, float $rate, float $gstRate, bool $interstate): array
    {
        $taxable = round($quantity * $rate, 2);
        $tax = round($taxable * $gstRate / 100, 2);

        if ($interstate) {
            $cgst = 0.0;
            $sgst = 0.0;
            $igst = $tax;
        } else {
            $cgst = round($tax / 2, 2);
            $sgst = round($tax - $cgst, 2); // keep cgst + sgst == tax exactly
            $igst = 0.0;
        }

        return [
            'taxable_amount' => $taxable,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => $igst,
            'line_total' => round($taxable + $cgst + $sgst + $igst, 2),
        ];
    }

    /**
     * Roll a set of computed line arrays up into invoice-level totals + words.
     *
     * @param  array<int, array<string, float>>  $lines
     * @return array{taxable_amount: float, cgst_amount: float, sgst_amount: float, igst_amount: float, grand_total: float, amount_in_words: string}
     */
    public static function aggregateTotals(array $lines): array
    {
        $taxable = 0.0;
        $cgst = 0.0;
        $sgst = 0.0;
        $igst = 0.0;

        foreach ($lines as $line) {
            $taxable += $line['taxable_amount'];
            $cgst += $line['cgst_amount'];
            $sgst += $line['sgst_amount'];
            $igst += $line['igst_amount'];
        }

        $grand = round($taxable + $cgst + $sgst + $igst, 2);

        return [
            'taxable_amount' => round($taxable, 2),
            'cgst_amount' => round($cgst, 2),
            'sgst_amount' => round($sgst, 2),
            'igst_amount' => round($igst, 2),
            'grand_total' => $grand,
            'amount_in_words' => IndianNumber::toWords($grand),
        ];
    }

    /**
     * Next invoice number: "{prefix}/{running serial}/{financial year}".
     * Serial restarts each Indian financial year (Apr–Mar), e.g. TINV/1/26-27.
     */
    public static function makeInvoiceNumber(\DateTimeInterface $date): string
    {
        $prefix = config('tax_invoice.number_prefix', 'TINV');

        $year = (int) $date->format('Y');
        $month = (int) $date->format('n');
        $fyStart = $month >= 4 ? $year : $year - 1;
        $fyLabel = sprintf('%02d-%02d', $fyStart % 100, ($fyStart + 1) % 100);

        // Count invoices already issued in this financial year (incl. soft-deleted
        // so a number is never reused after a removal).
        $fyStartDate = sprintf('%d-04-01', $fyStart);
        $fyEndDate = sprintf('%d-03-31', $fyStart + 1);

        $serial = static::withTrashed()
            ->whereBetween('invoice_date', [$fyStartDate, $fyEndDate])
            ->count() + 1;

        return sprintf('%s/%d/%s', $prefix, $serial, $fyLabel);
    }
}
