<?php

namespace App\Helpers;

/**
 * Global helper functions for Blade templates
 */

if (!function_exists('currency')) {
    /**
     * Format amount as currency with ₹ symbol
     * 
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    function currency($amount, $showSymbol = true)
    {
        return CurrencyHelper::format($amount, $showSymbol);
    }
}

if (!function_exists('currencyIndian')) {
    /**
     * Format amount with Indian numbering system
     * Example: ₹1,23,456.78
     * 
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    function currencyIndian($amount, $showSymbol = true)
    {
        return CurrencyHelper::formatIndian($amount, $showSymbol);
    }
}

if (!function_exists('currencyNoDecimal')) {
    /**
     * Format amount without decimal places
     * 
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    function currencyNoDecimal($amount, $showSymbol = true)
    {
        return CurrencyHelper::formatNoDecimal($amount, $showSymbol);
    }
}

if (!function_exists('currencyCustom')) {
    /**
     * Format amount with custom decimal places
     * 
     * @param float|int $amount
     * @param int $decimals
     * @param bool $showSymbol
     * @return string
     */
    function currencyCustom($amount, $decimals = 2, $showSymbol = true)
    {
        return CurrencyHelper::formatCustom($amount, $decimals, $showSymbol);
    }
}

if (!function_exists('currencySymbol')) {
    /**
     * Get currency symbol
     * 
     * @return string
     */
    function currencySymbol()
    {
        return CurrencyHelper::symbol();
    }
}

if (!function_exists('currencyCode')) {
    /**
     * Get currency code
     * 
     * @return string
     */
    function currencyCode()
    {
        return CurrencyHelper::code();
    }
}

if (!function_exists('parseCurrency')) {
    /**
     * Parse currency string to float
     * 
     * @param string $amount
     * @return float
     */
    function parseCurrency($amount)
    {
        return CurrencyHelper::parse($amount);
    }
}

