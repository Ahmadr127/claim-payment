<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed kelas kamar sesuai referensi tabel billing RS Azra.
 * Data ini bisa dikelola lebih lanjut via Admin UI.
 */
class RoomClassSeeder extends Seeder
{
    public function run(): void
    {
        $roomClasses = [
            ['code' => 'SUITES',    'name' => 'Suites',    'display_order' => 1],
            ['code' => 'VVIP',      'name' => 'VVIP',      'display_order' => 2],
            ['code' => 'VIP',       'name' => 'VIP',       'display_order' => 3],
            ['code' => 'UTAMA',     'name' => 'Utama',     'display_order' => 4],
            ['code' => 'KELAS_I',   'name' => 'Kelas I',   'display_order' => 5],
            ['code' => 'KELAS_II',  'name' => 'Kelas II',  'display_order' => 6],
            ['code' => 'KELAS_III', 'name' => 'Kelas III', 'display_order' => 7],
        ];

        foreach ($roomClasses as $class) {
            DB::table('room_classes')->updateOrInsert(
                ['code' => $class['code']],
                array_merge($class, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Tipe tarif kamar
        $tariffTypes = [
            ['code' => 'kamar_rawat',    'name' => 'Tarif Kamar Rawat'],
            ['code' => 'perawatan_umum', 'name' => 'Jasa Perawatan Umum'],
        ];

        foreach ($tariffTypes as $type) {
            DB::table('room_tariff_types')->updateOrInsert(
                ['code' => $type['code']],
                array_merge($type, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Tarif kamar per kelas (referensi tabel RS Azra Januari 2025)
        // QTY=3, jadi unit price = total / 3
        $kamarTariffs = [
            // [kode_kelas, tarif_kamar_per_hari, tarif_perawatan_per_hari]
            ['SUITES',    1600000, 215000],
            ['VVIP',      1300000, 180000],  // total VVIP: 3.900.000 / 3
            ['VIP',       1000000, 180000],
            ['UTAMA',      800000, 110000],
            ['KELAS_I',    700000, 100000],
            ['KELAS_II',   500000,  85000],
            ['KELAS_III',  250000,  20000],
        ];

        $kamarTypeId    = DB::table('room_tariff_types')->where('code', 'kamar_rawat')->value('id');
        $perawatanTypeId = DB::table('room_tariff_types')->where('code', 'perawatan_umum')->value('id');

        foreach ($kamarTariffs as [$classCode, $kamarAmount, $perawatanAmount]) {
            $classId = DB::table('room_classes')->where('code', $classCode)->value('id');
            if (! $classId) {
                continue;
            }

            DB::table('room_tariffs')->updateOrInsert(
                ['room_class_id' => $classId, 'room_tariff_type_id' => $kamarTypeId, 'effective_date' => '2025-01-01'],
                ['amount' => $kamarAmount, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );

            DB::table('room_tariffs')->updateOrInsert(
                ['room_class_id' => $classId, 'room_tariff_type_id' => $perawatanTypeId, 'effective_date' => '2025-01-01'],
                ['amount' => $perawatanAmount, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
