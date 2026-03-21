<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SellInvoice extends Model
{
    use HasFactory;

    public const TYPE_CASH = 'cash';

    public const TYPE_ONLINE = 'online';

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

    public function sells()
    {
        $fk = $this->invoice_type === self::TYPE_ONLINE
            ? 'online_sell_invoice_id'
            : 'cash_sell_invoice_id';

        return $this->hasMany(Sell::class, $fk);
    }

    public function getInvoiceTypeLabelAttribute(): string
    {
        return match ($this->invoice_type) {
            self::TYPE_CASH => 'Cash invoice',
            self::TYPE_ONLINE => 'Online payment invoice',
            default => ucfirst((string) $this->invoice_type),
        };
    }

    /**
     * Total amount attributed to this invoice type for one sale (full sell row).
     */
    public static function totalAmountForSell(Sell $sell, string $invoiceType): float
    {
        $mode = $sell->payment_mode ?? 'cash';

        if ($invoiceType === self::TYPE_CASH) {
            if ($mode === 'cash') {
                return (float) $sell->total_amount;
            }
            if ($mode === 'mix') {
                return (float) ($sell->cash_amount ?? 0);
            }

            return 0.0;
        }

        // online invoice
        if (in_array($mode, ['upi', 'gpay'], true)) {
            return (float) $sell->total_amount;
        }
        if ($mode === 'mix') {
            return (float) ($sell->online_amount ?? 0);
        }

        return 0.0;
    }

    /**
     * Line amount on this invoice (proportional split for mix).
     */
    public static function lineAmountForInvoiceType(Sell $sell, SellItem $item, string $invoiceType): float
    {
        $sellTotal = max((float) ($sell->total_amount ?? 0), 0.00001);
        $portion = (float) $item->total_price / $sellTotal;

        if ($invoiceType === self::TYPE_CASH) {
            if ($sell->payment_mode === 'cash') {
                return (float) $item->total_price;
            }
            if ($sell->payment_mode === 'mix') {
                return round($portion * (float) ($sell->cash_amount ?? 0), 2);
            }

            return 0.0;
        }

        if (in_array($sell->payment_mode, ['upi', 'gpay'], true)) {
            return (float) $item->total_price;
        }
        if ($sell->payment_mode === 'mix') {
            return round($portion * (float) ($sell->online_amount ?? 0), 2);
        }

        return 0.0;
    }

    /**
     * Aggregate totals for stored invoice snapshot (one column is non-zero per type).
     */
    public static function aggregateTotalsFromSells(Collection $sells, string $invoiceType): array
    {
        $total = 0.0;
        $cash = 0.0;
        $online = 0.0;

        foreach ($sells as $sell) {
            $amt = self::totalAmountForSell($sell, $invoiceType);
            $total += $amt;
            if ($invoiceType === self::TYPE_CASH) {
                $cash += $amt;
            } else {
                $online += $amt;
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
        $suffix = $type === self::TYPE_ONLINE ? 'ONL' : 'CASH';

        return 'SINV-' . $date->format('Ymd') . '-' . $suffix;
    }
}
