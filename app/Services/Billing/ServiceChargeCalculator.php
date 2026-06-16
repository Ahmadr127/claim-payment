<?php

namespace App\Services\Billing;

use App\Models\Patient\Hospitalization;
use App\Services\Service\ServiceTariffResolver;
use Carbon\Carbon;
use Exception;

class ServiceChargeCalculator
{
    protected ServiceTariffResolver $tariffResolver;

    public function __construct(ServiceTariffResolver $tariffResolver)
    {
        $this->tariffResolver = $tariffResolver;
    }

    /**
     * Calculate service charge (visit dokter, lab, radiologi) based on patient's room class.
     *
     * @param Hospitalization $hospitalization
     * @param int $medicalServiceId
     * @param int $qty
     * @param string|Carbon $chargeDate
     * @return array ['unit_price' => int, 'total_price' => int, 'tariff_id' => int]
     * @throws Exception
     */
    public function calculate(Hospitalization $hospitalization, int $medicalServiceId, int $qty, $chargeDate): array
    {
        $tariff = $this->tariffResolver->resolve(
            $medicalServiceId,
            $hospitalization->room_class_id,
            $chargeDate
        );

        if (!$tariff) {
            throw new Exception("Tarif jasa medis tidak ditemukan untuk layanan dan kelas ini pada tanggal {$chargeDate}.");
        }

        return [
            'unit_price' => $tariff->amount,
            'total_price' => $tariff->amount * $qty,
            'tariff_id' => $tariff->id,
        ];
    }
}
