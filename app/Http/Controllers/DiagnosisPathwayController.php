<?php

namespace App\Http\Controllers;

use App\Models\Patient\Diagnosis;
use App\Models\Patient\DiagnosisPathway;
use App\Models\Room\RoomClass;
use App\Models\Room\RoomTariffType;
use App\Models\Service\MedicalService;
use App\Models\Medication\Medication;
use Illuminate\Http\Request;

class DiagnosisPathwayController extends Controller
{
    public function show(Diagnosis $diagnosis)
    {
        $pathway = DiagnosisPathway::with(['items.item'])->where('diagnosis_id', $diagnosis->id)->first();
        $roomClasses = RoomClass::where('is_active', true)->orderBy('display_order')->get();

        if (!$pathway) {
            return redirect()->back()->with('error', 'Tarif Umum untuk diagnosa ini belum tersedia.');
        }

        $matrix = [];
        
        foreach ($pathway->items as $pathwayItem) {
            $item = $pathwayItem->item;
            if (!$item) continue;
            
            $type = class_basename($pathwayItem->item_type);
            
            $rowData = [
                'id' => $item->id,
                'type' => $type,
                'name' => $item->name,
                'description' => $item->description ?? '-',
                'code' => $item->code ?? $item->item_code ?? '-',
                'qty' => $pathwayItem->quantity,
                'tariffs' => [],
            ];

            foreach ($roomClasses as $rc) {
                $amount = 0;
                
                if ($type === 'RoomTariffType') {
                    $tariff = \Illuminate\Support\Facades\DB::table('room_tariffs')
                        ->where('room_class_id', $rc->id)
                        ->where('room_tariff_type_id', $item->id)
                        ->where('is_active', true)
                        ->first();
                    if ($tariff) $amount = $tariff->amount;
                } elseif ($type === 'MedicalService') {
                    $tariff = \Illuminate\Support\Facades\DB::table('service_tariffs')
                        ->where('room_class_id', $rc->id)
                        ->where('medical_service_id', $item->id)
                        ->where('is_active', true)
                        ->first();
                    if ($tariff) $amount = $tariff->amount;
                } elseif ($type === 'Medication') {
                    $tariff = \Illuminate\Support\Facades\DB::table('medication_tariffs')
                        ->where('room_class_id', $rc->id)
                        ->where('medication_id', $item->id)
                        ->where('is_active', true)
                        ->first();
                    if ($tariff) $amount = $tariff->amount;
                }

                $rowData['tariffs'][$rc->id] = [
                    'amount' => $amount,
                    'total' => $amount * $pathwayItem->quantity
                ];
            }

            $matrix[] = $rowData;
        }

        return view('diagnoses.pathway', compact('diagnosis', 'pathway', 'roomClasses', 'matrix'));
    }

    public function searchServices(Request $request)
    {
        $q = strtolower(trim($request->get('q', '')));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Search MedicalService
        MedicalService::whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(code) LIKE ?', ["%{$q}%"])
            ->limit(15)
            ->get()
            ->each(function ($item) use (&$results) {
                $results[] = [
                    'id'   => $item->id,
                    'code' => $item->code ?? '-',
                    'name' => $item->name,
                    'description' => $item->description ?? '-',
                    'type' => 'MedicalService',
                    'type_label' => 'Jasa Medis',
                ];
            });

        // Search Medication
        Medication::whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(item_code) LIKE ?', ["%{$q}%"])
            ->limit(15)
            ->get()
            ->each(function ($item) use (&$results) {
                $results[] = [
                    'id'   => $item->id,
                    'code' => $item->item_code ?? '-',
                    'name' => $item->name,
                    'description' => $item->description ?? '-',
                    'type' => 'Medication',
                    'type_label' => 'Obat & Alkes',
                ];
            });

        // Search RoomTariffType
        RoomTariffType::whereRaw('LOWER(name) LIKE ?', ["%{$q}%"])
            ->orWhereRaw('LOWER(code) LIKE ?', ["%{$q}%"])
            ->limit(10)
            ->get()
            ->each(function ($item) use (&$results) {
                $results[] = [
                    'id'   => $item->id,
                    'code' => $item->code ?? '-',
                    'name' => $item->name,
                    'description' => $item->description ?? '-',
                    'type' => 'RoomTariffType',
                    'type_label' => 'Tarif Kamar',
                ];
            });

        return response()->json($results);
    }

    public function update(Request $request, Diagnosis $diagnosis)
    {
        $pathway = DiagnosisPathway::where('diagnosis_id', $diagnosis->id)->firstOrFail();
        
        $matrix = $request->input('matrix', []);
        $adminFeePercentage = $request->input('admin_fee_percentage');
        
        if ($adminFeePercentage !== null) {
            $diagnosis->update([
                'admin_fee_percentage' => (float) $adminFeePercentage
            ]);
        }
        
        // Delete existing items
        $pathway->items()->delete();
        
        // Re-insert based on matrix
        $newItems = [];
        foreach ($matrix as $row) {
            if (!empty($row['deleted']) && $row['deleted'] == true) {
                continue;
            }
            
            $typeClass = '';
            if ($row['type'] === 'MedicalService') {
                $typeClass = 'App\\Models\\Service\\MedicalService';
            } elseif ($row['type'] === 'Medication') {
                $typeClass = 'App\\Models\\Medication\\Medication';
            } elseif ($row['type'] === 'RoomTariffType') {
                $typeClass = 'App\\Models\\Room\\RoomTariffType';
            }
            
            if ($typeClass && !empty($row['id'])) {
                $newItems[] = [
                    'diagnosis_pathway_id' => $pathway->id,
                    'item_type' => $typeClass,
                    'item_id' => $row['id'],
                    'quantity' => $row['qty'] ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        if (!empty($newItems)) {
            \App\Models\Patient\DiagnosisPathwayItem::insert($newItems);
        }
        
        return response()->json(['message' => 'Perubahan simulasi berhasil disimpan ke database.']);
    }
}
