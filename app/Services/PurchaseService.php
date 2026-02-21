<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Collection;

class PurchaseService
{
    /**
     * Calculate total amount from products array (store format).
     * Total = items subtotal + transportation (expense stored separately).
     */
    public function calculateTotalFromProducts(array $products, float $transportationCost = 0): float
    {
        $subtotal = 0.0;
        foreach ($products as $product) {
            $subtotal += ($product['quantity'] ?? 0) * ($product['purchase_price'] ?? 0);
        }
        return round($subtotal + $transportationCost, 2);
    }

    /**
     * Calculate total amount from flat arrays (update format).
     */
    public function calculateTotalFromArrays(array $productIds, array $quantities, array $purchasePrices, float $transportationCost = 0): float
    {
        $subtotal = 0.0;
        foreach ($productIds as $key => $productId) {
            $subtotal += ($quantities[$key] ?? 0) * ($purchasePrices[$key] ?? 0);
        }
        return round($subtotal + $transportationCost, 2);
    }

    /**
     * Create purchase items from products array (store format).
     */
    public function createItemsFromProducts(Purchase $purchase, array $products): Collection
    {
        $items = collect();

        foreach ($products as $product) {
            $quantity = (int) ($product['quantity'] ?? 0);
            $price = (float) ($product['purchase_price'] ?? 0);
            $totalPrice = round($quantity * $price, 2);

            $item = $purchase->items()->create([
                'product_id' => $product['product_id'],
                'quantity' => $quantity,
                'purchase_price' => $price,
                'total_price' => $totalPrice,
            ]);
            $items->push($item);
        }

        return $items;
    }

    /**
     * Create purchase items from flat arrays (update format).
     */
    public function createItemsFromArrays(Purchase $purchase, array $productIds, array $quantities, array $purchasePrices): Collection
    {
        $items = collect();

        foreach ($productIds as $key => $productId) {
            $quantity = (int) ($quantities[$key] ?? 0);
            $price = (float) ($purchasePrices[$key] ?? 0);
            $totalPrice = round($quantity * $price, 2);

            $item = $purchase->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'purchase_price' => $price,
                'total_price' => $totalPrice,
            ]);
            $items->push($item);
        }

        return $items;
    }

    /**
     * Build purchase attributes for create.
     */
    public function buildStoreAttributes(array $validated, float $totalAmount): array
    {
        return [
            'purchase_date' => $validated['purchase_date'],
            'supplier_name' => $validated['supplier_name'],
            'bill_type' => $validated['bill_type'],
            'bill_details' => $validated['bill_details'] ?? null,
            'transportation_cost' => (float) ($validated['transportation_cost'] ?? 0),
            'bill_due_date' => $validated['bill_due_date'] ?? null,
            'total_amount' => $totalAmount,
            'expense' => (float) ($validated['expense'] ?? 0),
            'expense_details' => $validated['expense_details'] ?? null,
        ];
    }

    /**
     * Build purchase attributes for update.
     */
    public function buildUpdateAttributes(array $validated, float $totalAmount): array
    {
        return [
            'purchase_date' => $validated['purchase_date'],
            'supplier_name' => $validated['supplier_name'],
            'bill_type' => $validated['bill_type'],
            'bill_details' => $validated['bill_details'] ?? null,
            'transportation_cost' => (float) ($validated['transportation_cost'] ?? 0),
            'bill_due_date' => $validated['bill_due_date'] ?? null,
            'total_amount' => $totalAmount,
            'expense' => (float) ($validated['expense'] ?? 0),
            'expense_details' => $validated['expense_details'] ?? null,
        ];
    }
}
