<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed obat dan alkes dari tabel RS Azra.
 * Item baru bisa ditambah via Admin UI tanpa ubah kode.
 */
class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori obat/alkes
        $categories = [
            ['code' => 'OBAT',     'name' => 'Obat-obatan'],
            ['code' => 'ALKES',    'name' => 'Alat Kesehatan'],
            ['code' => 'INFUS',    'name' => 'Cairan Infus'],
            ['code' => 'KONSUMABLE', 'name' => 'Barang Konsumable'],
        ];

        foreach ($categories as $cat) {
            DB::table('medication_categories')->updateOrInsert(
                ['code' => $cat['code']],
                array_merge($cat, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }

        $catId = fn(string $code) => DB::table('medication_categories')->where('code', $code)->value('id');

        // Obat dan alkes dari tabel RS Azra
        $medications = [
            // [item_code, nama, kategori, unit]
            ['OBTO1259', 'TERFACEF INJ',                    'OBAT',       'vial'],
            ['OBTO1085', 'PUMPITOR INJ 40 MG',              'OBAT',       'vial'],
            ['OBTO1320', 'TROVENSIS INJ 4 MG',              'OBAT',       'ampul'],
            ['OBTO1099', 'RANTIN INJ',                      'OBAT',       'ampul'],
            ['OBTO0651', 'INTERLAC CHEW TAB',               'OBAT',       'sachet'],
            ['ALK00368',  'HI-FRESH WASH CLOTH',             'KONSUMABLE', 'pcs'],
            ['OBTO2246', 'INFUSAN NaCL 100ML SANBE',        'INFUS',      'botol'],
            ['ALK00701',  'SPLIT 10CC',                      'ALKES',      'pcs'],
            ['ALK00706',  'SPLIT 3CC',                       'ALKES',      'pcs'],
            ['ALK01307',  'ALCOHOLSWAB BIRU COSMOMED-JS',    'KONSUMABLE', 'pcs'],
            ['ALK00224',  'ELASTOMULL 10CM X 4 MX - JS',    'ALKES',      'pcs'],
            ['OBTO2553', 'INFUSAN RL (RL)',                 'INFUS',      'botol'],
            ['ALK00380',  'INFUS SET',                       'ALKES',      'pcs'],
            ['ALK00759',  'SURSHIELD SURFLO II SAFETY NO 22 25', 'ALKES', 'pcs'],
        ];

        foreach ($medications as [$code, $name, $catCode, $unit]) {
            DB::table('medications')->updateOrInsert(
                ['item_code' => $code],
                [
                    'medication_category_id' => $catId($catCode),
                    'name'       => $name,
                    'unit'       => $unit,
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Tarif per kelas kamar (dari tabel RS Azra, unit price per item)
        // Format: [item_code, SUITES, VVIP, VIP, UTAMA, KELAS_I, KELAS_II, KELAS_III]
        $tariffs = [
            ['OBTO1259', 440877, 426181, 426181, 411485, 411485, 411485, 411485],
            ['OBTO1085', 306054, 295852, 295852, 285650, 285650, 285650, 285650],
            ['OBTO1320',  36029,  66728,  66728,  64427,  64427,  64427,  64427],
            ['OBTO1099',  49950,  48285,  48285,  46620,  46620,  46620,  46620],
            ['OBTO0651',  18870,  18241,  18241,  17612,  17612,  17612,  17612],
            ['ALK00368',  28305,  27362,  27362,  26418,  26418,  26418,  26418],
            ['OBTO2246',  31386,  29534,  29534,  28516,  28516,  28516,  28516],
            ['ALK00701',  13320,  12876,  12876,  12432,  12432,  12432,  12432],
            ['ALK00706',   9658,   8369,   8369,   8081,   8081,   8081,   8081],
            ['ALK01307',     804,    777,    777,    750,    750,    750,    750],
            ['ALK00224',  30116,  29112,  29112,  27104,  27104,  27104,  27104],
            ['OBTO2553',  38215,  35007,  35007,  33800,  33800,  33800,  33800],
            ['ALK00380',  21201,  21461,  21461,  20721,  20721,  20721,  20721],
            ['ALK00759',  11655,  11267,  11267,  10878,  10878,  10878,  10878],
        ];

        $classCodes = ['SUITES', 'VVIP', 'VIP', 'UTAMA', 'KELAS_I', 'KELAS_II', 'KELAS_III'];

        foreach ($tariffs as $tariff) {
            $itemCode    = array_shift($tariff);
            $medicationId = DB::table('medications')->where('item_code', $itemCode)->value('id');

            if (! $medicationId) {
                continue;
            }

            foreach ($tariff as $index => $amount) {
                $classCode = $classCodes[$index];
                $classId   = DB::table('room_classes')->where('code', $classCode)->value('id');

                if (! $classId) {
                    continue;
                }

                DB::table('medication_tariffs')->updateOrInsert(
                    ['medication_id' => $medicationId, 'room_class_id' => $classId, 'effective_date' => '2025-01-01'],
                    ['amount' => $amount, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
