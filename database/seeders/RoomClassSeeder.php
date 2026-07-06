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
            ['code' => '1',    'name' => 'Suites',    'display_order' => 1],
            ['code' => '2',    'name' => 'VVIP',      'display_order' => 2],
            ['code' => '3',    'name' => 'VIP',       'display_order' => 3],
            ['code' => '4',    'name' => 'Utama',     'display_order' => 4],
            ['code' => '5',    'name' => 'Kelas I',   'display_order' => 5],
            ['code' => '6',    'name' => 'Kelas II',  'display_order' => 6],
            ['code' => '7',    'name' => 'Kelas III', 'display_order' => 7],
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
            ['code' => '1',    'name' => 'Tarif Kamar Rawat'],
            ['code' => '2',    'name' => 'Jasa Perawatan Umum'],
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
            ['1',    1600000, 215000],
            ['2',    1300000, 180000],
            ['3',    1000000, 180000],
            ['4',     800000, 110000],
            ['5',     700000, 100000],
            ['6',     500000,  85000],
            ['7',     250000,  20000],
        ];

        $kamarTypeId    = DB::table('room_tariff_types')->where('code', '1')->value('id');
        $perawatanTypeId = DB::table('room_tariff_types')->where('code', '2')->value('id');

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
