<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class SaleInvoice extends Model
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
        'sales_count',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'invoice_type' => 'string',
        'total_amount' => 'float',
        'cash_total' => 'float',
        'online_total' => 'float',
        'sales_count' => 'integer',
    ];

    /**
     * Foreign key on `sales` for this invoice type (normalized — avoids Eloquent caching wrong hasMany FK).
     */
    public function invoiceForeignKey(): string
    {
        $type = strtolower(trim((string) ($this->attributes['invoice_type'] ?? $this->invoice_type ?? '')));

        return match ($type) {
            'online' => 'online_sale_invoice_id',
            'mix' => 'mix_sale_invoice_id',
            default => 'cash_sale_invoice_id',
        };
    }

    /**
     * Related sales — FK depends on invoice type (cash / online / mix are separate).
     */
    public function sales()
    {
        return $this->hasMany(Sale::class, $this->invoiceForeignKey());
    }

    /**
     * Load sales for view/export.
     * Uses all three possible FK columns — does not depend on invoice_type matching DB (fixes empty online views when type/FK were inconsistent).
     */
    public function loadSalesForDisplay(): Collection
    {
        $this->unsetRelation('sales');

        $invoiceId = (int) $this->id;

        $sales = Sale::query()
            ->with('items.product')
            ->where(function ($q) use ($invoiceId) {
                $q->where('cash_sale_invoice_id', $invoiceId)
                    ->orWhere('online_sale_invoice_id', $invoiceId)
                    ->orWhere('mix_sale_invoice_id', $invoiceId);
            })
            ->orderBy('id')
            ->get();

        $this->setRelation('sales', $sales);

        return $sales;
    }

    /**
     * Clear all invoice FKs on sales pointing to this invoice before soft-delete.
     */
    public function unlinkSalesFromInvoice(): void
    {
        $id = $this->id;

        Sale::where('cash_sale_invoice_id', $id)->update(['cash_sale_invoice_id' => null]);
        Sale::where('online_sale_invoice_id', $id)->update(['online_sale_invoice_id' => null]);
        Sale::where('mix_sale_invoice_id', $id)->update(['mix_sale_invoice_id' => null]);
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
     * Total amount for this invoice type (one full sale row where applicable).
     */
    public static function totalAmountForSale(Sale $sale, string $invoiceType): float
    {
        $mode = $sale->payment_mode ?? 'cash';

        return match ($invoiceType) {
            self::TYPE_CASH => $mode === 'cash' ? (float) $sale->total_amount : 0.0,
            self::TYPE_ONLINE => in_array($mode, ['upi', 'gpay'], true) ? (float) $sale->total_amount : 0.0,
            self::TYPE_MIX => $mode === 'mix' ? (float) $sale->total_amount : 0.0,
            default => 0.0,
        };
    }

    /**
     * Line amount on this invoice (full line for cash/online/mix invoices; no split).
     */
    public static function lineAmountForInvoiceType(Sale $sale, SaleItem $item, string $invoiceType): float
    {
        if (self::totalAmountForSale($sale, $invoiceType) <= 0) {
            return 0.0;
        }

        return (float) $item->total_price;
    }

    /**
     * Aggregate totals for stored invoice snapshot.
     */
    public static function aggregateTotalsFromSales(Collection $sales, string $invoiceType): array
    {
        $total = 0.0;
        $cash = 0.0;
        $online = 0.0;

        foreach ($sales as $sale) {
            $total += self::totalAmountForSale($sale, $invoiceType);

            if ($invoiceType === self::TYPE_MIX) {
                $cash += (float) ($sale->cash_amount ?? 0);
                $online += (float) ($sale->online_amount ?? 0);
            } elseif ($invoiceType === self::TYPE_CASH) {
                $cash += self::totalAmountForSale($sale, $invoiceType);
            } else {
                $online += self::totalAmountForSale($sale, $invoiceType);
            }
        }

        return [
            'total_amount' => round($total, 2),
            'cash_total' => round($cash, 2),
            'online_total' => round($online, 2),
            'sales_count' => $sales->count(),
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
