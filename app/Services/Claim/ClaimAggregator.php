<?php

namespace App\Services\Claim;

use App\Models\Patient\Hospitalization;
use App\Models\Billing\RoomCharge;
use App\Models\Billing\ServiceCharge;
use App\Models\Billing\MedicationCharge;
use Illuminate\Support\Collection;

class ClaimAggregator
{
    /**
     * Aggregate all unbilled charges from a hospitalization into a draft claim structure.
     * Note: This does not save to DB, it just prepares the data.
     * 
     * @param Hospitalization $hospitalization
     * @return array
     */
    public function aggregate(Hospitalization $hospitalization): array
    {
        $items = new Collection();
        $totalRoomCharge = 0;
        $totalServiceCharge = 0;
        $totalMedicationCharge = 0;

        // Helper query to get billed charge IDs
        $billedRoomChargeIds = \App\Models\Claim\ClaimItem::where('charge_type', 'room')->pluck('charge_id');
        $billedServiceChargeIds = \App\Models\Claim\ClaimItem::where('charge_type', 'service')->pluck('charge_id');
        $billedMedicationChargeIds = \App\Models\Claim\ClaimItem::where('charge_type', 'medication')->pluck('charge_id');

        // 1. Room Charges
        $roomCharges = RoomCharge::with(['roomTariff.roomTariffType'])
            ->where('hospitalization_id', $hospitalization->id)
            ->whereNotIn('id', $billedRoomChargeIds)
            ->get();

        foreach ($roomCharges as $charge) {
            $items->push([
                'category' => 'room',
                'item_code' => $charge->roomTariff->roomTariffType->code ?? 'ROOM',
                'item_name' => $charge->roomTariff->roomTariffType->name ?? 'Tarif Kamar',
                'qty' => $charge->qty,
                'unit_price' => $charge->unit_price,
                'total_price' => $charge->total_price,
                'reference_id' => $charge->id,
                'reference_type' => RoomCharge::class,
            ]);
            $totalRoomCharge += $charge->total_price;
        }

        // 2. Service Charges
        $serviceCharges = ServiceCharge::with(['serviceTariff.medicalService'])
            ->where('hospitalization_id', $hospitalization->id)
            ->whereNotIn('id', $billedServiceChargeIds)
            ->get();

        foreach ($serviceCharges as $charge) {
            $items->push([
                'category' => 'service',
                'item_code' => $charge->serviceTariff->medicalService->code ?? 'SVC',
                'item_name' => $charge->serviceTariff->medicalService->name ?? 'Jasa Medis',
                'qty' => $charge->qty,
                'unit_price' => $charge->unit_price,
                'total_price' => $charge->total_price,
                'reference_id' => $charge->id,
                'reference_type' => ServiceCharge::class,
            ]);
            $totalServiceCharge += $charge->total_price;
        }

        // 3. Medication Charges
        $medicationCharges = MedicationCharge::with(['medicationTariff.medication'])
            ->where('hospitalization_id', $hospitalization->id)
            ->whereNotIn('id', $billedMedicationChargeIds)
            ->get();

        foreach ($medicationCharges as $charge) {
            $items->push([
                'category' => 'medication',
                'item_code' => $charge->medicationTariff->medication->item_code ?? 'MED',
                'item_name' => $charge->medicationTariff->medication->name ?? 'Obat/Alkes',
                'qty' => $charge->qty,
                'unit_price' => $charge->unit_price,
                'total_price' => $charge->total_price,
                'reference_id' => $charge->id,
                'reference_type' => MedicationCharge::class,
            ]);
            $totalMedicationCharge += $charge->total_price;
        }

        $grandTotal = $totalRoomCharge + $totalServiceCharge + $totalMedicationCharge;

        return [
            'total_room_charge' => $totalRoomCharge,
            'total_service_charge' => $totalServiceCharge,
            'total_medication_charge' => $totalMedicationCharge,
            'grand_total' => $grandTotal,
            'items' => $items,
        ];
    }
}
