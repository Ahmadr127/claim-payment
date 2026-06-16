<?php

namespace Database\Seeders;

use App\Models\Patient\Diagnosis;
use App\Models\Patient\DiagnosisPathway;
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
        $suites = RoomClass::where('code', 'SUITES')->first();
        $vvip = RoomClass::where('code', 'VVIP')->first();
        $vip = RoomClass::where('code', 'VIP')->first();
        $utama = RoomClass::where('code', 'UTAMA')->first();
        $kelasI = RoomClass::where('code', 'KELAS_I')->first();
        $kelasII = RoomClass::where('code', 'KELAS_II')->first();
        $kelasIII = RoomClass::where('code', 'KELAS_III')->first();

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
                $cat = DB::table('service_categories')->where('code', 'CAT_'.$code)->first();
                $catId = $cat ? $cat->id : DB::table('service_categories')->insertGetId(['code' => 'CAT_'.$code, 'name' => 'Kategori '.$name]);
                $item = $modelClass::firstOrCreate(['code' => $code], ['name' => $name, 'service_category_id' => $catId, 'is_active' => true]);
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
        $addItem('RoomTariffType', RoomTariffType::class, 'TARIF KAMAR RAWAT', 'KAMAR', 3, 
            [1600000, 1300000, 1000000, 800000, 700000, 500000, 250000]);
        $addItem('RoomTariffType', RoomTariffType::class, 'JASA PERAWATAN UMUM', 'RAWAT', 3, 
            [215000, 180000, 180000, 110000, 100000, 85000, 20000]);

        // --- Medical Services ---
        $addItem('MedicalService', MedicalService::class, 'JASA VISIT DOKTER SPESIALIS PENYAKIT DALAM', 'VISIT_SPESIALIS', 3, 
            [280000, 260000, 260000, 230000, 230000, 220000, 210000]);
        $addItem('MedicalService', MedicalService::class, 'Jasa Visite Dokter Umum Ruangan', 'VISIT_UMUM', 1, 
            [180000, 160000, 160000, 130000, 130000, 120000, 120000]);
            
        // Lab & Rad
        $addItem('MedicalService', MedicalService::class, 'Hematologi Lengkap', 'LAB_HEMA', 1, 
            [400000, 358000, 348000, 338000, 338000, 328000, 318000]);
        $addItem('MedicalService', MedicalService::class, 'USG ABDOMEN', 'RAD_USG', 1, 
            [1000000, 900000, 900000, 726000, 726000, 726000, 726000]);

        // --- Medications ---
        $addItem('Medication', Medication::class, 'TERFACEF INJ', 'OBT01259', 6, 
            [440877, 426181, 426181, 411485, 411485, 411485, 411485]);
        $addItem('Medication', Medication::class, 'SANMOL INF', 'OBT01168', 6, 
            [124565, 120413, 120413, 116261, 116261, 116261, 116261]);
            
    }
}
