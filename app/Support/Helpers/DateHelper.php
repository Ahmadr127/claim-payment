<?php

namespace App\Support\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Calculate duration in days between two dates (inclusive of admission day)
     */
    public static function durationInDays(Carbon $from, Carbon $to): int
    {
        $days = (int) $from->diffInDays($to);
        return max(1, $days);
    }

    /**
     * Format date to Indonesian format
     */
    public static function formatIndonesian(Carbon $date): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return $date->day . ' ' . $months[$date->month] . ' ' . $date->year;
    }

    /**
     * Format date to standard display
     */
    public static function formatDisplay(Carbon $date): string
    {
        return $date->format('d/m/Y');
    }

    /**
     * Format datetime to display
     */
    public static function formatDatetime(Carbon $date): string
    {
        return $date->format('d/m/Y H:i');
    }
}
