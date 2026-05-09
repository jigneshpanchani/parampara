<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Status filter values used by Current Stock / Stock Closing screens.
     */
    public const STATUS_ALL      = 'all';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_ALL,
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    public const STATUS_LABELS = [
        self::STATUS_ALL      => 'All',
        self::STATUS_ACTIVE   => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected $fillable = [
        'product_name',
        'product_code',
        'description',
        'base_price_min',
        'base_price_max',
        'selling_price',
        'photo',
        'stock_quantity',
        'is_active',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'base_price_min' => 'float',
        'base_price_max' => 'float',
        'selling_price' => 'float',
        'is_active' => 'boolean',
    ];

    /*
     * Relationships
     */

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    /**
     * Get sales through sale items (hasManyThrough).
     */
    public function sales()
    {
        return $this->hasManyThrough(Sale::class, SaleItem::class);
    }

    /*
     * Scopes
     */

    public function scopeInStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->where('stock_quantity', '>', $threshold);
    }

    public function scopeLowStock(Builder $query, int $threshold = 10): Builder
    {
        return $query->whereBetween('stock_quantity', [1, $threshold]);
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    public function scopeOrderByName(Builder $query): Builder
    {
        return $query->orderBy('product_name');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter by Product::STATUS_* constant. STATUS_ALL (or anything unknown) → no filter.
     */
    public function scopeFilterByStatus(Builder $query, ?string $status): Builder
    {
        return match ($status) {
            self::STATUS_ACTIVE   => $query->where('is_active', true),
            self::STATUS_INACTIVE => $query->where('is_active', false),
            default               => $query,
        };
    }

    /**
     * Normalize an arbitrary input to a valid status constant; defaults to STATUS_ALL.
     */
    public static function normalizeStatus(?string $status): string
    {
        return in_array($status, self::STATUSES, true) ? $status : self::STATUS_ALL;
    }

    /*
     * Accessors & Helpers
     */

    /**
     * Get formatted base price range.
     */
    public function getBasePriceRangeFormattedAttribute(): string
    {
        return '₹' . number_format($this->base_price_min, 2) . ' - ₹' . number_format($this->base_price_max, 2);
    }

    /**
     * Get formatted selling price.
     */
    public function getSellingPriceFormattedAttribute(): string
    {
        return '₹' . number_format($this->selling_price, 2);
    }

    /**
     * Check if product has any transaction history.
     */
    public function hasTransactionHistory(): bool
    {
        return $this->purchaseItems()->exists()
            || $this->saleItems()->exists()
            || $this->purchaseReturns()->exists()
            || $this->saleReturns()->exists();
    }

    /**
     * Get current stock quantity
     */
    public function getCurrentStock()
    {
        return $this->stock_quantity ?? 0;
    }

    /**
     * Get stock status
     */
    public function getStockStatus()
    {
        $quantity = $this->getCurrentStock();

        if ($quantity <= 0) {
            return 'out_of_stock';
        } elseif ($quantity <= 10) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Get stock status color
     */
    public function getStockStatusColor()
    {
        return match($this->getStockStatus()) {
            'in_stock' => 'green',
            'low_stock' => 'yellow',
            'out_of_stock' => 'red',
            default => 'gray',
        };
    }

    /**
     * Increment stock
     */
    public function incrementStock($quantity)
    {
        $this->stock_quantity = ($this->stock_quantity ?? 0) + $quantity;
        $this->save();
    }

    /**
     * Decrement stock
     */
    public function decrementStock($quantity)
    {
        $this->stock_quantity = max(0, ($this->stock_quantity ?? 0) - $quantity);
        $this->save();
    }
}
