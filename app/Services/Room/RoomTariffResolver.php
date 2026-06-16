<?php

namespace App\Services\Room;

use App\Models\Room\RoomTariff;
use Carbon\Carbon;

class RoomTariffResolver
{
    /**
     * Get the active room tariff for a specific room class and type on a given date.
     * 
     * @param int $roomClassId
     * @param int $tariffTypeId
     * @param string|Carbon $date
     * @return RoomTariff|null
     */
    public function resolve(int $roomClassId, int $tariffTypeId, $date): ?RoomTariff
    {
        $date = $date instanceof Carbon ? $date->toDateString() : $date;

        return RoomTariff::where('room_class_id', $roomClassId)
            ->where('room_tariff_type_id', $tariffTypeId)
            ->where('is_active', true)
            ->where('effective_date', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('expired_date')
                      ->orWhere('expired_date', '>=', $date);
            })
            ->orderByDesc('effective_date')
            ->first();
    }
}
