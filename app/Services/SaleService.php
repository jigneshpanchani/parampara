<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Collection;

class SaleService
{
    /**
     * Calculate total amount from product items.
     */
    public function calculateTotalAmount(array $productIds, array $quantities, array $sellingPrices): float
    {
        $total = 0.0;
        foreach ($productIds as $key => $productId) {
            $total += ($quantities[$key] ?? 0) * ($sellingPrices[$key] ?? 0);
        }
        return round($total, 2);
    }

    /**
     * Resolve cash and online amounts for mix payment mode.
     */
    public function resolveMixPayment(array $validated): array
    {
        $cashAmount = 0.0;
        $onlineAmount = 0.0;

        if (($validated['payment_mode'] ?? '') === 'mix') {
            $cashAmount = (float) ($validated['cash_amount'] ?? 0);
            $onlineAmount = (float) ($validated['online_amount'] ?? 0);
            $validated['amount_paid'] = $cashAmount + $onlineAmount;
        }

        return [
            'cash_amount' => $cashAmount,
            'online_amount' => $onlineAmount,
            'amount_paid' => (float) ($validated['amount_paid'] ?? 0),
        ];
    }

    /**
     * Determine payment status and pending amount.
     */
    public function resolvePaymentStatus(float $totalAmount, float $amountPaid): array
    {
        $pendingAmount = max($totalAmount - $amountPaid, 0);
        $paymentStatus = 'pending';

        if ($amountPaid >= $totalAmount && $totalAmount > 0) {
            $paymentStatus = 'paid';
            $pendingAmount = 0;
        } elseif ($amountPaid > 0) {
            $paymentStatus = 'partial';
        }

        return [
            'payment_status' => $paymentStatus,
            'pending_amount' => $pendingAmount,
        ];
    }

    /**
     * Create sale items for a sale.
     */
    public function createSaleItems(Sale $sale, array $productIds, array $quantities, array $sellingPrices): Collection
    {
        $items = collect();

        foreach ($productIds as $key => $productId) {
            $quantity = (int) ($quantities[$key] ?? 0);
            $price = (float) ($sellingPrices[$key] ?? 0);
            $totalPrice = round($quantity * $price, 2);

            $item = SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'selling_price' => $price,
                'total_price' => $totalPrice,
            ]);
            $items->push($item);
        }

        return $items;
    }

    /**
     * Build sale attributes for create/update.
     */
    public function buildSaleAttributes(array $validated, float $totalAmount): array
    {
        $payment = $this->resolveMixPayment($validated);
        $status = $this->resolvePaymentStatus($totalAmount, $payment['amount_paid']);

        return [
            'sale_date' => $validated['sale_date'],
            'seller_name' => $validated['seller_name'] ?? null,
            'seller_contact_number' => $validated['seller_contact_number'] ?? null,
            'total_amount' => $totalAmount,
            'payment_mode' => $validated['payment_mode'],
            'payment_status' => $status['payment_status'],
            'amount_paid' => $payment['amount_paid'],
            'cash_amount' => $payment['cash_amount'],
            'online_amount' => $payment['online_amount'],
            'pending_amount' => $status['pending_amount'],
            'notes' => $validated['notes'] ?? null,
        ];
    }
}
