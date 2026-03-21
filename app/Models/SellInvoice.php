<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class SellInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const TYPE_CASH = 'cash';

    public const TYPE_ONLINE = 'online';

    public const TYPE_MIX = 'mix';

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'invoice_type',
        'total_amount',
        'cash_total',
        'online_total',
        'sells_count',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total_amount' => 'float',
        'cash_total' => 'float',
        'online_total' => 'float',
        'sells_count' => 'integer',
    ];

    /**
     * Related sells — FK depends on invoice type (cash / online / mix are separate).
     */
    public function sells()
    {
        return match ($this->invoice_type) {
            self::TYPE_ONLINE => $this->hasMany(Sell::class, 'online_sell_invoice_id'),
            self::TYPE_MIX => $this->hasMany(Sell::class, 'mix_sell_invoice_id'),
            default => $this->hasMany(Sell::class, 'cash_sell_invoice_id'),
        };
    }

    public function getInvoiceTypeLabelAttribute(): string
    {
        return match ($this->invoice_type) {
            self::TYPE_CASH => 'Cash invoice',
            self::TYPE_ONLINE => 'Online payment invoice',
            self::TYPE_MIX => 'Mix payment invoice',
            default => ucfirst((string) $this->invoice_type),
        };
    }

    /**
     * Total amount for this invoice type (one full sell row where applicable).
     */
    public static function totalAmountForSell(Sell $sell, string $invoiceType): float
    {
        $mode = $sell->payment_mode ?? 'cash';

        return match ($invoiceType) {
            self::TYPE_CASH => $mode === 'cash' ? (float) $sell->total_amount : 0.0,
            self::TYPE_ONLINE => in_array($mode, ['upi', 'gpay'], true) ? (float) $sell->total_amount : 0.0,
            self::TYPE_MIX => $mode === 'mix' ? (float) $sell->total_amount : 0.0,
            default => 0.0,
        };
    }

    /**
     * Line amount on this invoice (full line for cash/online/mix invoices; no split).
     */
    public static function lineAmountForInvoiceType(Sell $sell, SellItem $item, string $invoiceType): float
    {
        if (self::totalAmountForSell($sell, $invoiceType) <= 0) {
            return 0.0;
        }

        return (float) $item->total_price;
    }

    /**
     * Aggregate totals for stored invoice snapshot.
     */
    public static function aggregateTotalsFromSells(Collection $sells, string $invoiceType): array
    {
        $total = 0.0;
        $cash = 0.0;
        $online = 0.0;

        foreach ($sells as $sell) {
            $total += self::totalAmountForSell($sell, $invoiceType);

            if ($invoiceType === self::TYPE_MIX) {
                $cash += (float) ($sell->cash_amount ?? 0);
                $online += (float) ($sell->online_amount ?? 0);
            } elseif ($invoiceType === self::TYPE_CASH) {
                $cash += self::totalAmountForSell($sell, $invoiceType);
            } else {
                $online += self::totalAmountForSell($sell, $invoiceType);
            }
        }

        return [
            'total_amount' => round($total, 2),
            'cash_total' => round($cash, 2),
            'online_total' => round($online, 2),
            'sells_count' => $sells->count(),
        ];
    }

    public static function makeInvoiceNumber(\DateTimeInterface $date, string $type): string
    {
        $suffix = match ($type) {
            self::TYPE_ONLINE => 'ONLINE',
            self::TYPE_MIX => 'MIX',
            default => 'CASH',
        };

        return 'SINV-' . $date->format('Ymd') . '-' . $suffix;
    }
}
