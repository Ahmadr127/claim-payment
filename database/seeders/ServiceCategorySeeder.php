<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed kategori jasa medis dan layanan-layanannya.
 * Referensi: tabel billing RS Azra — kategori & item bisa ditambah via Admin.
 */
class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'perawatan_umum',        'name' => 'Jasa Perawatan Umum',                   'display_order' => 1],
            ['code' => 'visit_spesialis',        'name' => 'Jasa Visit Dokter Spesialis',           'display_order' => 2],
            ['code' => 'visite_umum',            'name' => 'Jasa Visite Dokter Umum Ruangan',       'display_order' => 3],
            ['code' => 'konsultasi_spesialis',   'name' => 'Jasa Konsultasi Dokter Spesialis',      'display_order' => 4],
            ['code' => 'laboratorium',           'name' => 'Laboratorium',                          'display_order' => 5],
            ['code' => 'radiologi',              'name' => 'Radiologi',                             'display_order' => 6],
            ['code' => 'tindakan_medis',         'name' => 'Tindakan Medis',                        'display_order' => 7],
        ];

        foreach ($categories as $cat) {
            DB::table('service_categories')->updateOrInsert(
                ['code' => $cat['code']],
                array_merge($cat, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // Helper
        $catId = fn(string $code) => DB::table('service_categories')->where('code', $code)->value('id');

        // Layanan medis spesifik (dari tabel RS Azra)
        $services = [
            ['category' => 'visit_spesialis',      'code' => 'VISIT-SPESIALIS-ANAK',  'name' => 'Jasa Visit Dokter Spesialis Anak',           'unit' => 'kali'],
            ['category' => 'visite_umum',          'code' => 'VISITE-DOKTER-UMUM',    'name' => 'Jasa Visite Dokter Umum Ruangan',            'unit' => 'kali'],
            ['category' => 'konsultasi_spesialis', 'code' => 'KONSUL-BEDAH-UMUM',     'name' => 'Jasa Konsultasi Dokter Spesialis Bedah Umum', 'unit' => 'kali'],
            ['category' => 'laboratorium',         'code' => 'LAB-HEMATOLOGI',         'name' => 'Hematologi Lengkap',                         'unit' => 'paket'],
            ['category' => 'laboratorium',         'code' => 'LAB-URINE',              'name' => 'Urine Lengkap',                              'unit' => 'paket'],
            ['category' => 'laboratorium',         'code' => 'LAB-CRP-KUANTITATIF',    'name' => 'CRP Kuantitatif',                            'unit' => 'paket'],
            ['category' => 'radiologi',            'code' => 'RAD-USG-ABDOMEN',        'name' => 'USG Abdomen',                                'unit' => 'paket'],
        ];

        foreach ($services as $svc) {
            DB::table('medical_services')->updateOrInsert(
                ['code' => $svc['code']],
                [
                    'service_category_id' => $catId($svc['category']),
                    'name'       => $svc['name'],
                    'unit'       => $svc['unit'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Tarif layanan per kelas kamar (dari tabel RS Azra, Januari 2025)
        // Format: [service_code, SUITES, VVIP, VIP, UTAMA, KELAS_I, KELAS_II, KELAS_III]
        $tariffs = [
            ['VISIT-SPESIALIS-ANAK',  840000, 780000, 780000, 690000, 690000, 660000, 630000],
            ['VISITE-DOKTER-UMUM',    180000, 160000, 160000, 130000, 130000, 120000, 120000],
            ['KONSUL-BEDAH-UMUM',     280000, 260000, 260000, 280000, 230000, 220000, 210000],
            ['LAB-HEMATOLOGI',        400000, 358000, 348000, 338000, 338000, 328000, 318000],
            ['LAB-URINE',             130000, 120000, 110000, 100000, 100000, 100000, 100000],
            ['LAB-CRP-KUANTITATIF',   360000, 320000, 310000, 300000, 300000, 300000, 300000],
            ['RAD-USG-ABDOMEN',      1000000, 900000, 900000, 726000, 726000, 726000, 726000],
        ];

        $classCodes = ['SUITES', 'VVIP', 'VIP', 'UTAMA', 'KELAS_I', 'KELAS_II', 'KELAS_III'];

        foreach ($tariffs as $tariff) {
            $serviceCode = array_shift($tariff);
            $serviceId   = DB::table('medical_services')->where('code', $serviceCode)->value('id');

            if (! $serviceId) {
                continue;
            }

            foreach ($tariff as $index => $amount) {
                $classCode = $classCodes[$index];
                $classId   = DB::table('room_classes')->where('code', $classCode)->value('id');

                if (! $classId) {
                    continue;
                }

                DB::table('service_tariffs')->updateOrInsert(
                    ['medical_service_id' => $serviceId, 'room_class_id' => $classId, 'effective_date' => '2025-01-01'],
                    ['amount' => $amount, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
