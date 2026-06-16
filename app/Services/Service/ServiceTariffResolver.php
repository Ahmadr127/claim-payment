<?php

namespace App\Services\Service;

use App\Models\Service\ServiceTariff;
use Carbon\Carbon;

class ServiceTariffResolver
{
    /**
     * Get the active service tariff for a specific medical service and room class on a given date.
     * 
     * @param int $medicalServiceId
     * @param int|null $roomClassId
     * @param string|Carbon $date
     * @return ServiceTariff|null
     */
    public function resolve(int $medicalServiceId, ?int $roomClassId, $date): ?ServiceTariff
    {
        $date = $date instanceof Carbon ? $date->toDateString() : $date;

        $query = ServiceTariff::where('medical_service_id', $medicalServiceId)
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
