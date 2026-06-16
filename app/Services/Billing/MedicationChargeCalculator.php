<?php

namespace App\Services\Billing;

use App\Models\Patient\Hospitalization;
use App\Services\Medication\MedicationTariffResolver;
use Carbon\Carbon;
use Exception;

class MedicationChargeCalculator
{
    protected MedicationTariffResolver $tariffResolver;

    public function __construct(MedicationTariffResolver $tariffResolver)
    {
        $this->tariffResolver = $tariffResolver;
    }

    /**
     * Calculate medication/consumable charge based on patient's room class.
     *
     * @param Hospitalization $hospitalization
     * @param int $medicationId
     * @param int $qty
     * @param string|Carbon $chargeDate
     * @return array ['unit_price' => int, 'total_price' => int, 'tariff_id' => int]
     * @throws Exception
     */
    public function calculate(Hospitalization $hospitalization, int $medicationId, int $qty, $chargeDate): array
    {
        $tariff = $this->tariffResolver->resolve(
            $medicationId,
            $hospitalization->room_class_id,
            $chargeDate
        );

        if (!$tariff) {
            throw new Exception("Tarif obat/alkes tidak ditemukan untuk item dan kelas ini pada tanggal {$chargeDate}.");
        }

        return [
            'unit_price' => $tariff->amount,
            'total_price' => $tariff->amount * $qty,
            'tariff_id' => $tariff->id,
        ];
    }
}
