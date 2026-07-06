<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed golongan layanan medis dan layanan-layanannya.
 * Referensi: tabel billing RS Azra — golongan & item bisa ditambah via Admin.
 */
class ServiceGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['code' => '10',    'name' => 'Jasa Perawatan Umum',                   'display_order' => 1],
            ['code' => '20',    'name' => 'Jasa Visit Dokter Spesialis',           'display_order' => 2],
            ['code' => '30',    'name' => 'Jasa Visite Dokter Umum Ruangan',       'display_order' => 3],
            ['code' => '40',    'name' => 'Jasa Konsultasi Dokter Spesialis',      'display_order' => 4],
            ['code' => '50',    'name' => 'Laboratorium',                          'display_order' => 5],
            ['code' => '60',    'name' => 'Radiologi',                             'display_order' => 6],
            ['code' => '70',    'name' => 'Tindakan Medis',                        'display_order' => 7],
        ];

        foreach ($groups as $group) {
            DB::table('service_groups')->updateOrInsert(
                ['code' => $group['code']],
                array_merge($group, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        // Helper
        $groupId = fn(string $code) => DB::table('service_groups')->where('code', $code)->value('id');

        // Layanan medis spesifik (dari tabel RS Azra)
        $services = [
            ['group' => '20',   'code' => '2001',  'name' => 'Jasa Visit Dokter Spesialis Anak',           'unit' => 'kali'],
            ['group' => '30',   'code' => '2002',  'name' => 'Jasa Visite Dokter Umum Ruangan',            'unit' => 'kali'],
            ['group' => '40',   'code' => '2003',  'name' => 'Jasa Konsultasi Dokter Spesialis Bedah Umum', 'unit' => 'kali'],
            ['group' => '50',   'code' => '2004',  'name' => 'Hematologi Lengkap',                         'unit' => 'paket'],
            ['group' => '50',   'code' => '2005',  'name' => 'Urine Lengkap',                              'unit' => 'paket'],
            ['group' => '50',   'code' => '2006',  'name' => 'CRP Kuantitatif',                            'unit' => 'paket'],
            ['group' => '60',   'code' => '2007',  'name' => 'USG Abdomen',                                'unit' => 'paket'],
        ];

        foreach ($services as $svc) {
            DB::table('medical_services')->updateOrInsert(
                ['code' => $svc['code']],
                [
                    'service_group_id' => $groupId($svc['group']),
                    'name'       => $svc['name'],
                    'unit'       => $svc['unit'],
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Tarif layanan per kelas kamar (dari tabel RS Azra, Januari 2025)
        // Format: [service_code, KELAS_1, KELAS_2, KELAS_3, KELAS_4, KELAS_5, KELAS_6, KELAS_7]
        $tariffs = [
            ['2001',  840000, 780000, 780000, 690000, 690000, 660000, 630000],
            ['2002',  180000, 160000, 160000, 130000, 130000, 120000, 120000],
            ['2003',  280000, 260000, 260000, 280000, 230000, 220000, 210000],
            ['2004',  400000, 358000, 348000, 338000, 338000, 328000, 318000],
            ['2005',  130000, 120000, 110000, 100000, 100000, 100000, 100000],
            ['2006',  360000, 320000, 310000, 300000, 300000, 300000, 300000],
            ['2007', 1000000, 900000, 900000, 726000, 726000, 726000, 726000],
        ];

        $classCodes = ['1', '2', '3', '4', '5', '6', '7'];

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
