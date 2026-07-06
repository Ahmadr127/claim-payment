<?php

namespace Database\Seeders;

use App\Models\ClinicalPathway\Diagnosis;
use App\Models\ClinicalPathway\DiagnosisPathway;
use App\Models\Room\RoomTariffType;
use App\Models\Service\MedicalService;
use App\Models\Medication\Medication;
use App\Models\Room\RoomClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiagnosisPathwaySeeder extends Seeder
{
    public function run(): void
    {
        // Get or Create Diagnosis Cholecystitis
        $diagnosis = Diagnosis::firstOrCreate(
            ['icd_code' => 'K81.9'],
            ['name' => 'CHOLECISTITIS (NON BEDAH)', 'is_active' => true]
        );

        // Get Room Classes
        $suites = RoomClass::where('code', '1')->first();
        $vvip = RoomClass::where('code', '2')->first();
        $vip = RoomClass::where('code', '3')->first();
        $utama = RoomClass::where('code', '4')->first();
        $kelasI = RoomClass::where('code', '5')->first();
        $kelasII = RoomClass::where('code', '6')->first();
        $kelasIII = RoomClass::where('code', '7')->first();

        // 1. Create Pathway
        $pathway = DiagnosisPathway::firstOrCreate([
            'diagnosis_id' => $diagnosis->id,
            'length_of_stay' => 3
        ]);

        $pathway->items()->delete();

        // --- Helper function to attach items ---
        $addItem = function ($type, $modelClass, $name, $code, $qty, $tariffs) use ($pathway, $suites, $vvip, $vip, $utama, $kelasI, $kelasII, $kelasIII) {
            // Find or Create Item
            if ($modelClass === RoomTariffType::class) {
                $item = $modelClass::firstOrCreate(['code' => $code], ['name' => $name, 'is_active' => true]);
                $tableName = 'room_tariffs';
                $foreignId = 'room_tariff_type_id';
            } elseif ($modelClass === MedicalService::class) {
                $grp = DB::table('service_groups')->where('code', 'GRP_'.$code)->first();
                $grpId = $grp ? $grp->id : DB::table('service_groups')->insertGetId(['code' => 'GRP_'.$code, 'name' => 'Golongan '.$name, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
                $item = $modelClass::firstOrCreate(['code' => $code], ['name' => $name, 'service_group_id' => $grpId, 'is_active' => true]);
                $tableName = 'service_tariffs';
                $foreignId = 'medical_service_id';
            } else {
                $cat = DB::table('medication_categories')->where('code', 'CAT_'.$code)->first();
                $catId = $cat ? $cat->id : DB::table('medication_categories')->insertGetId(['code' => 'CAT_'.$code, 'name' => 'Kategori '.$name]);
                $item = $modelClass::firstOrCreate(['item_code' => $code], ['name' => $name, 'medication_category_id' => $catId, 'is_active' => true]);
                $tableName = 'medication_tariffs';
                $foreignId = 'medication_id';
            }

            // Create pathway item
            $pathway->items()->create([
                'item_type' => $modelClass,
                'item_id' => $item->id,
                'quantity' => $qty
            ]);

            // Create Tariffs if tariffs passed (suites, vvip, vip, utama, 1, 2, 3)
            $classes = [$suites, $vvip, $vip, $utama, $kelasI, $kelasII, $kelasIII];
            foreach ($classes as $idx => $rc) {
                if ($rc && isset($tariffs[$idx])) {
                    DB::table($tableName)->updateOrInsert([
                        'room_class_id' => $rc->id,
                        $foreignId => $item->id,
                        'effective_date' => date('Y-01-01')
                    ], [
                        'amount' => $tariffs[$idx],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        };


        // --- Room Tariffs ---
        $addItem('RoomTariffType', RoomTariffType::class, 'Tarif Kamar Rawat', '1', 3, 
            [1600000, 1300000, 1000000, 800000, 700000, 500000, 250000]);
        $addItem('RoomTariffType', RoomTariffType::class, 'Jasa Perawatan Umum', '2', 3, 
            [215000, 180000, 180000, 110000, 100000, 85000, 20000]);

        // --- Medical Services ---
        $addItem('MedicalService', MedicalService::class, 'Jasa Visit Dokter Spesialis Penyakit Dalam', '2001', 3, 
            [280000, 260000, 260000, 230000, 230000, 220000, 210000]);
        $addItem('MedicalService', MedicalService::class, 'Jasa Visite Dokter Umum Ruangan', '2002', 1, 
            [180000, 160000, 160000, 130000, 130000, 120000, 120000]);
            
        // Lab & Rad
        $addItem('MedicalService', MedicalService::class, 'Hematologi Lengkap', '2003', 1, 
            [400000, 358000, 348000, 338000, 338000, 328000, 318000]);
        $addItem('MedicalService', MedicalService::class, 'USG ABDOMEN', '2004', 1, 
            [1000000, 900000, 900000, 726000, 726000, 726000, 726000]);

        // --- Medications ---
        $addItem('Medication', Medication::class, 'TERFACEF INJ', '3001', 6, 
            [440877, 426181, 426181, 411485, 411485, 411485, 411485]);
        $addItem('Medication', Medication::class, 'SANMOL INF', '3002', 6, 
            [124565, 120413, 120413, 116261, 116261, 116261, 116261]);
            
    }
}
