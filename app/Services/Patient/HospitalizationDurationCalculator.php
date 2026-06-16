<?php

namespace App\Services\Patient;

use Carbon\Carbon;

class HospitalizationDurationCalculator
{
    /**
     * Calculate the duration of hospitalization in days.
     * Minimum 1 day for same-day discharge.
     * 
     * @param string|Carbon $admittedAt
     * @param string|Carbon $dischargedAt
     * @return int
     */
    public function calculate($admittedAt, $dischargedAt): int
    {
        $admitted = $admittedAt instanceof Carbon ? $admittedAt : Carbon::parse($admittedAt);
        $discharged = $dischargedAt instanceof Carbon ? $dischargedAt : Carbon::parse($dischargedAt);

        // Calculate diff in days (start of day to start of day)
        $diff = $admitted->startOfDay()->diffInDays($discharged->startOfDay());

        // Even if same day, it counts as 1 day of room usage
        return $diff > 0 ? $diff : 1;
    }
}
