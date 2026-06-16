<?php

namespace App\Services\Medication;

use App\Models\Medication\MedicationTariff;
use Carbon\Carbon;

class MedicationTariffResolver
{
    /**
     * Get the active medication tariff for a specific medication and room class on a given date.
     * 
     * @param int $medicationId
     * @param int|null $roomClassId
     * @param string|Carbon $date
     * @return MedicationTariff|null
     */
    public function resolve(int $medicationId, ?int $roomClassId, $date): ?MedicationTariff
    {
        $date = $date instanceof Carbon ? $date->toDateString() : $date;

        $query = MedicationTariff::where('medication_id', $medicationId)
            ->where('is_active', true)
            ->where('effective_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('expired_date')
                  ->orWhere('expired_date', '>=', $date);
            });

        if ($roomClassId !== null) {
            $query->where('room_class_id', $roomClassId);
        }

        return $query->orderByDesc('effective_date')->first();
    }
}
