<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Format amount with Indian Rupees symbol
     * 
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    public static function format($amount, $showSymbol = true)
    {
        if ($amount === null) {
            return $showSymbol ? '₹0.00' : '0.00';
        }

        // Convert to float
        $amount = (float) $amount;

        // Format with 2 decimal places
        $formatted = number_format($amount, 2, '.', ',');

        // Add symbol if requested
        return $showSymbol ? '₹' . $formatted : $formatted;
    }

    /**
     * Format amount with Indian numbering system
     * Example: 1,23,456.78
     * 
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    public static function formatIndian($amount, $showSymbol = true)
    {
        if ($amount === null) {
            return $showSymbol ? '₹0.00' : '0.00';
        }

        $amount = (float) $amount;
        $amountStr = number_format($amount, 2, '.', '');
        
        list($integerPart, $decimalPart) = explode('.', $amountStr);
        
        $lastThree = substr($integerPart, -3);
        $otherNumbers = substr($integerPart, 0, -3);
        
        $formattedIntegerPart = $otherNumbers !== ''
            ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $otherNumbers) . ',' . $lastThree
            : $lastThree;
        
        $formatted = $formattedIntegerPart . '.' . $decimalPart;

        return $showSymbol ? '₹' . $formatted : $formatted;
    }

    /**
     * Format amount without decimal places
     * 
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    public static function formatNoDecimal($amount, $showSymbol = true)
    {
        if ($amount === null) {
            return $showSymbol ? '₹0' : '0';
        }

        $amount = (int) round((float) $amount);
        $formatted = number_format($amount, 0, '.', ',');

        return $showSymbol ? '₹' . $formatted : $formatted;
    }

    /**
     * Format amount with custom decimal places
     * 
     * @param float|int $amount
     * @param int $decimals
     * @param bool $showSymbol
     * @return string
     */
    public static function formatCustom($amount, $decimals = 2, $showSymbol = true)
    {
        if ($amount === null) {
            $zero = str_repeat('0', $decimals > 0 ? $decimals + 1 : 0);
            return $showSymbol ? '₹' . $zero : $zero;
        }

        $amount = (float) $amount;
        $formatted = number_format($amount, $decimals, '.', ',');

        return $showSymbol ? '₹' . $formatted : $formatted;
    }

    /**
     * Get currency symbol
     * 
     * @return string
     */
    public static function symbol()
    {
        return '₹';
    }

    /**
     * Get currency code
     * 
     * @return string
     */
    public static function code()
    {
        return 'INR';
    }

    /**
     * Parse currency string to float
     * Removes ₹ symbol and commas
     * 
     * @param string $amount
     * @return float
     */
    public static function parse($amount)
    {
        if (is_numeric($amount)) {
            return (float) $amount;
        }

        // Remove ₹ symbol and commas
        $cleaned = str_replace(['₹', ','], '', $amount);
        
        return (float) $cleaned;
    }
}

