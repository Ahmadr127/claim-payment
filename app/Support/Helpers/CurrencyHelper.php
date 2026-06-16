<?php

namespace App\Support\Helpers;

class CurrencyHelper
{
    /**
     * Format amount to Indonesian Rupiah string
     */
    public static function format(int|float $amount, bool $withSymbol = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');

        return $withSymbol ? "Rp {$formatted}" : $formatted;
    }

    /**
     * Parse formatted Rupiah string back to integer
     */
    public static function parse(string $formatted): int
    {
        $cleaned = str_replace(['Rp', '.', ' '], '', $formatted);
        $cleaned = str_replace(',', '.', $cleaned);

        return (int) $cleaned;
    }

    /**
     * Calculate percentage of amount
     */
    public static function percentage(int|float $amount, float $percent): float
    {
        return round($amount * ($percent / 100), 0);
    }

    /**
     * Calculate total from qty and unit price
     */
    public static function totalCharge(int $qty, int|float $unitPrice): int
    {
        return (int) round($qty * $unitPrice);
    }
}
