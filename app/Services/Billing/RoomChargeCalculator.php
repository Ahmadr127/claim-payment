<?php

namespace App\Services\Billing;

use App\Models\Patient\Hospitalization;
use App\Services\Room\RoomTariffResolver;
use Carbon\Carbon;
use Exception;

class RoomChargeCalculator
{
    protected RoomTariffResolver $tariffResolver;

    public function __construct(RoomTariffResolver $tariffResolver)
    {
        $this->tariffResolver = $tariffResolver;
    }

    /**
     * Calculate room charge (kamar_rawat, perawatan_umum) based on patient's room class.
     *
     * @param Hospitalization $hospitalization
     * @param int $tariffTypeId
     * @param int $qty
     * @param string|Carbon $chargeDate
     * @return array ['unit_price' => int, 'total_price' => int, 'tariff_id' => int]
     * @throws Exception
     */
    public function calculate(Hospitalization $hospitalization, int $tariffTypeId, int $qty, $chargeDate): array
    {
        $tariff = $this->tariffResolver->resolve(
            $hospitalization->room_class_id,
            $tariffTypeId,
            $chargeDate
        );

        if (!$tariff) {
            throw new Exception("Tarif kamar tidak ditemukan untuk kelas ini pada tanggal {$chargeDate}.");
        }

        return [
            'unit_price' => $tariff->amount,
            'total_price' => $tariff->amount * $qty,
            'tariff_id' => $tariff->id,
        ];
    }
}
