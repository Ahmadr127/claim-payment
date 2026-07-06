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
        // 1. Kategori obat/alkes
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

        // 2. Golongan Obat
        $groups = [
            ['code' => '8', 'name' => 'OBAT UNTUK TERAPI PALIATIF'],
            ['code' => '28', 'name' => 'VITAMIN DAN MINERAL'],
            ['code' => '30', 'name' => 'LAIN - LAIN'],
        ];
        foreach ($groups as $g) {
            DB::table('medication_groups')->updateOrInsert(
                ['code' => $g['code']],
                array_merge($g, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()])
            );
        }
        $getGroupId = fn($code) => DB::table('medication_groups')->where('code', $code)->value('id');

        // 3. Komoditi
        $commodities = [
            'Apotik/Obat Injeksi',
            'Cairan Infus',
            'Apotik/Obat Resep (Selain Injeksi)/Ling K',
            'Alkes',
        ];
        foreach ($commodities as $c) {
            DB::table('medication_commodities')->updateOrInsert(
                ['name' => $c],
                ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
        $getCommId = fn($name) => DB::table('medication_commodities')->where('name', $name)->value('id');

        // 4. Kelompok Barang
        $prodGroups = [
            'PARENTERAL DAN VAKSIN',
            'INFUS',
            'ORAL',
            'KONSUMABLE',
            'ALKES'
        ];
        foreach ($prodGroups as $pg) {
            DB::table('medication_product_groups')->updateOrInsert(
                ['name' => $pg],
                ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
        $getProdGroupId = fn($name) => DB::table('medication_product_groups')->where('name', $name)->value('id');

        // 5. Obat dan alkes dari tabel RS Azra
        $medications = [
            // [code, name, category, unit, group_code, commodity_name, product_group_name, hna, hna_ppn, ppn_rajal, ppn_ranap, active_ingredient, composition, indication]
            ['OBTO1259', 'TERFACEF INJ',                    'OBAT',       'vial',   '8',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', 380000.00, 421800.00, 11.00, 11.00, 'Ceftriaxone', 'Ceftriaxone 1g', 'Infeksi bakteri gram negatif berat'],
            ['OBTO1085', 'PUMPITOR INJ 40 MG',              'OBAT',       'vial',   '8',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', null, null, 0, 0, null, null, null],
            ['OBTO1320', 'TROVENSIS INJ 4 MG',              'OBAT',       'ampul',  '8',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', null, null, 0, 0, null, null, null],
            ['OBTO1099', 'RANTIN INJ',                      'OBAT',       'ampul',  '8',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', null, null, 0, 0, null, null, null],
            ['OBTO0651', 'INTERLAC CHEW TAB',               'OBAT',       'sachet', '28', 'Apotik/Obat Resep (Selain Injeksi)/Ling K', 'ORAL',                  null, null, 0, 0, null, null, null],
            ['ALK00368', 'HI-FRESH WASH CLOTH',             'KONSUMABLE', 'pcs',    '30', 'Alkes',                                    'KONSUMABLE',            null, null, 0, 0, null, null, null],
            ['OBTO2246', 'INFUSAN NaCL 100ML SANBE',        'INFUS',      'botol',  '30', 'Cairan Infus',                             'INFUS',                 25000.00, 27750.00, 11.00, 11.00, 'Sodium Chloride', 'NaCl 0.9%', 'Mengganti cairan tubuh yang hilang'],
            ['ALK00701', 'SPLIT 10CC',                      'ALKES',      'pcs',    '30', 'Alkes',                                    'ALKES',                 null, null, 0, 0, null, null, null],
            ['ALK00706', 'SPLIT 3CC',                       'ALKES',      'pcs',    '30', 'Alkes',                                    'ALKES',                 null, null, 0, 0, null, null, null],
            ['ALK01307', 'ALCOHOLSWAB BIRU COSMOMED-JS',    'KONSUMABLE', 'pcs',    '30', 'Alkes',                                    'KONSUMABLE',            null, null, 0, 0, null, null, null],
            ['ALK00224', 'ELASTOMULL 10CM X 4 MX - JS',     'ALKES',      'pcs',    '30', 'Alkes',                                    'ALKES',                 null, null, 0, 0, null, null, null],
            ['OBTO2553', 'INFUSAN RL (RL)',                 'INFUS',      'botol',  '30', 'Cairan Infus',                             'INFUS',                 null, null, 0, 0, null, null, null],
            ['ALK00380', 'INFUS SET',                       'ALKES',      'pcs',    '30', 'Alkes',                                    'ALKES',                 null, null, 0, 0, null, null, null],
            ['ALK00759', 'SURSHIELD SURFLO II SAFETY NO 22 25', 'ALKES',  'pcs',    '30', 'Alkes',                                    'ALKES',                 null, null, 0, 0, null, null, null],
        ];

        foreach ($medications as $med) {
            DB::table('medications')->updateOrInsert(
                ['item_code' => $med[0]],
                [
                    'medication_category_id'      => $catId($med[2]),
                    'name'                        => $med[1],
                    'unit'                        => $med[3],
                    'medication_group_id'         => $getGroupId($med[4]),
                    'medication_commodity_id'     => $getCommId($med[5]),
                    'medication_product_group_id' => $getProdGroupId($med[6]),
                    'hna'                         => $med[7],
                    'hna_ppn'                     => $med[8],
                    'ppn_rajal'                   => $med[9],
                    'ppn_ranap'                   => $med[10],
                    'active_ingredient'           => $med[11],
                    'detailed_composition'        => $med[12],
                    'indication'                  => $med[13],
                    'is_active'                   => true,
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                ]
            );
        }

        // 6. Tarif per kelas kamar (dari tabel RS Azra, unit price per item)
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
