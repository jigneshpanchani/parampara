<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'product_code',
        'description',
        'base_price_min',
        'base_price_max',
        'sell_price',
        'photo',
        'stock_quantity',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'base_price_min' => 'float',
        'base_price_max' => 'float',
        'sell_price' => 'float',
    ];

    /*
     * Relationships
     */

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function sellItems()
    {
        return $this->hasMany(SellItem::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function sellReturns()
    {
        return $this->hasMany(SellReturn::class);
    }

    /**
     * Get sells through sell items (hasManyThrough).
     */
    public function sells()
    {
        return $this->hasManyThrough(Sell::class, SellItem::class);
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
     * Get formatted sell price.
     */
    public function getSellPriceFormattedAttribute(): string
    {
        return '₹' . number_format($this->sell_price, 2);
    }

    /**
     * Check if product has any transaction history.
     */
    public function hasTransactionHistory(): bool
    {
        return $this->purchaseItems()->exists()
            || $this->sellItems()->exists()
            || $this->purchaseReturns()->exists()
            || $this->sellReturns()->exists();
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
