<?php

namespace App\Services;

use App\Models\ClinicalPathway\Diagnosis;
use App\Models\OrganizationUnit;
use App\Models\Room\RoomClass;
use Illuminate\Support\Facades\DB;

class UnitCostCalculationService
{
    /**
     * Calculate unit cost matrix for a diagnosis and organization unit
     * LOGIC:
     * - Room: Ambil amount dari room_tariffs table (per room class)
     * - Medical Service: room_tariff_amount × percentage (percentage editable, default 70%)
     * - Medication: HNA + (HNA × PPN%)
     */
    public function calculateMatrix(Diagnosis $diagnosis, OrganizationUnit $organizationUnit): array
    {
        $pathway = $diagnosis->pathway;
        if (!$pathway) {
            return [];
        }

        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();
        $matrix = [];

        foreach ($pathway->items as $pathwayItem) {
            $item = $pathwayItem->item;
            if (!$item) continue;

            $type = class_basename($pathwayItem->item_type);

            $rowData = [
                'id' => $item->id,
                'type' => $type,
                'name' => $item->name,
                'code' => $item->code ?? $item->item_code ?? '-',
                'qty' => $pathwayItem->quantity,
                'tariffs' => [],
            ];

            // Ambil tariff amount untuk setiap room class
            foreach ($roomClasses as $rc) {
                $amount = 0;
                $baseAmount = 0;
                $hna = null;
                $ppn = null;
                $percentage = null;

                if ($type === 'RoomTariffType') {
                    // Kamar: ambil amount dari room_tariffs
                    $tariff = DB::table('room_tariffs')
                        ->where('room_tariff_type_id', $item->id)
                        ->where('room_class_id', $rc->id)
                        ->where('is_active', true)
                        ->latest('effective_date')
                        ->first();
                    $amount = $tariff?->amount ?? 0;
                    $baseAmount = $amount;
                    $hna = $amount;
                } elseif ($type === 'MedicalService') {
                    // Jasa Medis: room_tariff_amount × percentage
                    // Cari room tariff pertama (baseline) untuk perhitungan
                    $kamarType = DB::table('room_tariff_types')
                        ->where('code', '1')
                        ->orWhere('code', '1001')
                        ->orWhere('name', 'like', '%Kamar Rawat%')
                        ->first();

                    $roomTariff = DB::table('room_tariffs')
                        ->where('room_class_id', $rc->id)
                        ->when($kamarType, function($q) use ($kamarType) {
                            return $q->where('room_tariff_type_id', $kamarType->id);
                        })
                        ->where('is_active', true)
                        ->latest('effective_date')
                        ->first();
                    
                    $baseAmount = $roomTariff?->amount ?? 0;
                    $percentage = $item->percentage ?? 70; // Default 70%
                    $amount = (int) round($baseAmount * ($percentage / 100));
                    $hna = $baseAmount;
                } elseif ($type === 'Medication') {
                    // Obat/Alkes: HNA + (HNA × PPN%)
                    $hna = $item->hna ?? 0;
                    $ppn = $item->ppn_percentage ?? 0;
                    
                    if ($hna > 0) {
                        $amount = (int) round($hna * (1 + ($ppn / 100)));
                    }
                    $baseAmount = $amount;
                }

                $rowData['tariffs'][$rc->id] = [
                    'amount' => $amount,
                    'base_amount' => $baseAmount,
                    'hna' => $hna,
                    'ppn' => $ppn,
                    'hna_ppn' => ($type === 'Medication') ? $amount : null,
                    'percentage' => $percentage,
                    'total' => $amount * $pathwayItem->quantity
                ];
            }

            $matrix[] = $rowData;
        }

        return $matrix;
    }

    /**
     * Get summary totals per category
     */
    public function calculateCategorySummary(array $matrix, int $roomClassId): array
    {
        $summary = [
            'RoomTariffType' => 0,
            'MedicalService' => 0,
            'Medication' => 0,
            'total' => 0,
        ];

        foreach ($matrix as $row) {
            $type = $row['type'];
            $total = $row['tariffs'][$roomClassId]['total'] ?? 0;
            
            if (isset($summary[$type])) {
                $summary[$type] += $total;
            }
        }

        $summary['total'] = $summary['RoomTariffType'] + $summary['MedicalService'] + $summary['Medication'];

        return $summary;
    }
}
