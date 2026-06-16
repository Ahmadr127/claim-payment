<?php

namespace App\Services\Billing;

use App\Models\Claim\Claim;

class BillSummaryBuilder
{
    /**
     * Build a structured billing summary from a Claim model.
     * 
     * @param Claim $claim
     * @return array
     */
    public function build(Claim $claim): array
    {
        // Ensure relationships are loaded
        $claim->loadMissing([
            'hospitalization.diagnosis',
            'hospitalization.doctor',
            'roomClass',
            'patient',
            'items'
        ]);

        $hospitalization = $claim->hospitalization;
        $items = $claim->items;

        $roomGroup = $items->where('category', 'room')->values();
        $serviceGroup = $items->where('category', 'service')->values();
        $medicationGroup = $items->where('category', 'medication')->values();

        $subtotalRoom = $roomGroup->sum('total_price');
        $subtotalService = $serviceGroup->sum('total_price');
        $subtotalMedication = $medicationGroup->sum('total_price');

        $totalBeforeAdmin = $subtotalRoom + $subtotalService + $subtotalMedication;

        // Calculate dynamic admin fee
        $adminPercentage = config('billing.admin_fee_percentage', 6);
        $adminFee = (int) round($totalBeforeAdmin * ($adminPercentage / 100));

        $grandTotal = $totalBeforeAdmin + $adminFee;

        // Calculate length of stay if needed (assumes duration calculator logic)
        $admitted = $hospitalization->admitted_at;
        $discharged = $hospitalization->discharged_at;
        $lengthOfStay = $admitted && $discharged 
            ? max(1, $admitted->startOfDay()->diffInDays($discharged->startOfDay())) 
            : 1;

        return [
            'registration_number' => $hospitalization->registration_number ?? 'REG-' . str_pad($hospitalization->id, 5, '0', STR_PAD_LEFT),
            'patient_name' => $claim->patient->name ?? '-',
            'room_class' => $claim->roomClass->name ?? '-',
            'length_of_stay' => $lengthOfStay . ' Hari',
            'diagnosis' => $hospitalization->diagnosis->name ?? '-',
            'doctor' => $hospitalization->doctor->name ?? '-',
            
            'groups' => [
                [
                    'name' => 'BIAYA KAMAR & PERAWATAN',
                    'items' => $roomGroup->map(fn($item) => [
                        'item_code' => $item->item_code,
                        'item_name' => $item->item_name,
                        'qty' => $item->qty,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ]),
                    'subtotal' => $subtotalRoom
                ],
                [
                    'name' => 'JASA MEDIS (DOKTER, LAB, RAD)',
                    'items' => $serviceGroup->map(fn($item) => [
                        'item_code' => $item->item_code,
                        'item_name' => $item->item_name,
                        'qty' => $item->qty,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ]),
                    'subtotal' => $subtotalService
                ],
                [
                    'name' => 'OBAT & ALKES',
                    'items' => $medicationGroup->map(fn($item) => [
                        'item_code' => $item->item_code,
                        'item_name' => $item->item_name,
                        'qty' => $item->qty,
                        'unit_price' => $item->unit_price,
                        'total_price' => $item->total_price,
                    ]),
                    'subtotal' => $subtotalMedication
                ]
            ],

            'summary' => [
                'total_before_admin' => $totalBeforeAdmin,
                'admin_percentage' => $adminPercentage,
                'admin_fee' => $adminFee,
                'grand_total' => $grandTotal
            ]
        ];
    }
}
