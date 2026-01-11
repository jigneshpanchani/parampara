<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SellItem;
use App\Models\PurchaseReturn;
use App\Models\SellReturn;

class StockService
{
    /**
     * Add stock from purchase
     */
    public static function addStockFromPurchase($purchaseItems)
    {
        foreach ($purchaseItems as $item) {
            if ($item instanceof PurchaseItem) {
                $product = $item->product;
                if ($product) {
                    $product->incrementStock($item->quantity);
                }
            }
        }
    }

    /**
     * Remove stock from purchase (when purchase is deleted)
     */
    public static function removeStockFromPurchase($purchaseItems)
    {
        foreach ($purchaseItems as $item) {
            if ($item instanceof PurchaseItem) {
                $product = $item->product;
                if ($product) {
                    $product->decrementStock($item->quantity);
                }
            }
        }
    }

    /**
     * Deduct stock from sale
     */
    public static function deductStockFromSale($sellItems)
    {
        foreach ($sellItems as $item) {
            if ($item instanceof SellItem) {
                $product = $item->product;
                if ($product) {
                    $product->decrementStock($item->quantity);
                }
            }
        }
    }

    /**
     * Add stock back from sale (when sale is deleted)
     */
    public static function addStockBackFromSale($sellItems)
    {
        foreach ($sellItems as $item) {
            if ($item instanceof SellItem) {
                $product = $item->product;
                if ($product) {
                    $product->incrementStock($item->quantity);
                }
            }
        }
    }

    /**
     * Deduct stock from purchase return
     */
    public static function deductStockFromPurchaseReturn(PurchaseReturn $return)
    {
        $product = $return->product;
        if ($product) {
            $product->decrementStock($return->quantity);
        }
    }

    /**
     * Add stock back from purchase return (when return is deleted)
     */
    public static function addStockBackFromPurchaseReturn(PurchaseReturn $return)
    {
        $product = $return->product;
        if ($product) {
            $product->incrementStock($return->quantity);
        }
    }

    /**
     * Add stock from sell return
     */
    public static function addStockFromSellReturn(SellReturn $return)
    {
        $product = $return->product;
        if ($product) {
            $product->incrementStock($return->quantity);
        }
    }

    /**
     * Remove stock from sell return (when return is deleted)
     */
    public static function removeStockFromSellReturn(SellReturn $return)
    {
        $product = $return->product;
        if ($product) {
            $product->decrementStock($return->quantity);
        }
    }

    /**
     * Get product stock summary
     */
    public static function getStockSummary()
    {
        $products = Product::all();
        
        return [
            'total_products' => $products->count(),
            'in_stock' => $products->where('stock_quantity', '>', 10)->count(),
            'low_stock' => $products->whereBetween('stock_quantity', [1, 10])->count(),
            'out_of_stock' => $products->where('stock_quantity', '<=', 0)->count(),
            'total_value' => $products->sum(fn($p) => ($p->stock_quantity ?? 0) * $p->sell_price),
        ];
    }
}

