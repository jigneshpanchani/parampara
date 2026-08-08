<?php

namespace App\Support;

/**
 * Convert amounts to words using the Indian numbering system
 * (Thousand / Lakh / Crore), e.g. 125118 → "One Lakh Twenty Five
 * Thousand One Hundred Eighteen Only".
 */
class IndianNumber
{
    private const ONES = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen',
    ];

    private const TENS = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety',
    ];

    /**
     * Full rupee amount to words with a trailing "Only" (and paise if present).
     */
    public static function toWords(float $amount): string
    {
        $amount = round($amount, 2);
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        $words = self::integerToWords($rupees);
        if ($words === '') {
            $words = 'Zero';
        }

        if ($paise > 0) {
            $words .= ' and ' . self::integerToWords($paise) . ' Paise';
        }

        return $words . ' Only';
    }

    /**
     * Non-negative integer to Indian-system words. Returns '' for 0.
     */
    private static function integerToWords(int $number): string
    {
        if ($number <= 0) {
            return '';
        }

        $parts = [];

        $crore = intdiv($number, 10000000);
        $number %= 10000000;
        $lakh = intdiv($number, 100000);
        $number %= 100000;
        $thousand = intdiv($number, 1000);
        $hundreds = $number % 1000;

        if ($crore) {
            $parts[] = self::integerToWords($crore) . ' Crore';
        }
        if ($lakh) {
            $parts[] = self::twoDigits($lakh) . ' Lakh';
        }
        if ($thousand) {
            $parts[] = self::twoDigits($thousand) . ' Thousand';
        }
        if ($hundreds) {
            $parts[] = self::threeDigits($hundreds);
        }

        return trim(implode(' ', $parts));
    }

    /** 0..99 to words. */
    private static function twoDigits(int $n): string
    {
        if ($n < 20) {
            return self::ONES[$n];
        }

        $ones = $n % 10;

        return trim(self::TENS[intdiv($n, 10)] . ($ones ? ' ' . self::ONES[$ones] : ''));
    }

    /** 0..999 to words. */
    private static function threeDigits(int $n): string
    {
        $hundred = intdiv($n, 100);
        $rest = $n % 100;

        $parts = [];
        if ($hundred) {
            $parts[] = self::ONES[$hundred] . ' Hundred';
        }
        if ($rest) {
            $parts[] = self::twoDigits($rest);
        }

        return implode(' ', $parts);
    }
}
