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
            ['code' => '4001',     'name' => 'Obat-obatan'],
            ['code' => '4002',    'name' => 'Alat Kesehatan'],
            ['code' => '4003',    'name' => 'Cairan Infus'],
            ['code' => '4004', 'name' => 'Barang Konsumable'],
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
            ['code' => '5001', 'name' => 'OBAT UNTUK TERAPI PALIATIF'],
            ['code' => '5002', 'name' => 'VITAMIN DAN MINERAL'],
            ['code' => '5003', 'name' => 'LAIN - LAIN'],
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
            // [code, name, category, unit, group_code, commodity_name, product_group_name, hna, hna_ppn, ppn_percentage, active_ingredient, composition, indication]
            ['3001', 'TERFACEF INJ',                    '4001',       'vial',   '5001',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', 380000.00, 421800.00, 11.00, 'Ceftriaxone', 'Ceftriaxone 1g', 'Infeksi bakteri gram negatif berat'],
            ['3002', 'PUMPITOR INJ 40 MG',              '4001',       'vial',   '5001',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', 260000.00, 288600.00, 11.00, 'Omeprazole', 'Omeprazole 40mg', 'Terapi jangka pendek untuk ulkus peptikum'],
            ['3003', 'TROVENSIS INJ 4 MG',              '4001',       'ampul',  '5001',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', 30000.00, 33300.00, 11.00, 'Ondansetron', 'Ondansetron 4mg', 'Pencegahan mual muntah paska operasi'],
            ['3004', 'RANTIN INJ',                      '4001',       'ampul',  '5001',  'Apotik/Obat Injeksi',                      'PARENTERAL DAN VAKSIN', 40000.00, 44400.00, 11.00, 'Ranitidine', 'Ranitidine 25mg/ml', 'Terapi penyakit refluks gastroesofageal'],
            ['3005', 'INTERLAC CHEW TAB',               '4001',       'sachet', '5002', 'Apotik/Obat Resep (Selain Injeksi)/Ling K', 'ORAL',                  15000.00, 16650.00, 11.00, 'Lactobacillus reuteri', 'Lactobacillus reuteri Protectis', 'Memelihara kesehatan pencernaan anak'],
            ['3006', 'HI-FRESH WASH CLOTH',             '4004', 'pcs',    '5003', 'Alkes',                                    'KONSUMABLE',            22000.00, 24420.00, 11.00, null, 'Tisue basah antiseptik', 'Pembersih tubuh pasien tanpa air'],
            ['3007', 'INFUSAN NaCL 100ML SANBE',        '4003',      'botol',  '5003', 'Cairan Infus',                             'INFUS',                 25000.00, 27750.00, 11.00, 'Sodium Chloride', 'NaCl 0.9%', 'Mengganti cairan tubuh yang hilang'],
            ['3008', 'SPLIT 10CC',                      '4002',      'pcs',    '5003', 'Alkes',                                    'ALKES',                 10000.00, 11100.00, 11.00, null, 'Spuit 10cc', 'Alat injeksi sekali pakai'],
            ['3009', 'SPLIT 3CC',                       '4002',      'pcs',    '5003', 'Alkes',                                    'ALKES',                 8000.00, 8880.00, 11.00, null, 'Spuit 3cc', 'Alat injeksi sekali pakai'],
            ['3010', 'ALCOHOLSWAB BIRU COSMOMED-JS',    '4004', 'pcs',    '5003', 'Alkes',                                    'KONSUMABLE',            700.00, 777.00, 11.00, 'Isopropyl alcohol', 'Alkohol swab 70%', 'Antiseptik kulit sebelum penyuntikan'],
            ['3011', 'ELASTOMULL 10CM X 4 MX - JS',     '4002',      'pcs',    '5003', 'Alkes',                                    'ALKES',                 18000.00, 19980.00, 11.00, null, 'Perban elastis', 'Pembalut elastis penahan luka'],
            ['3012', 'INFUSAN RL (RL)',                 '4003',      'botol',  '5003', 'Cairan Infus',                             'INFUS',                 22000.00, 24420.00, 11.00, 'Ringer Lactate', 'Cairan Ringer Laktat', 'Resusitasi cairan tubuh yang hilang'],
            ['3013', 'INFUS SET',                       '4002',      'pcs',    '5003', 'Alkes',                                    'ALKES',                 15000.00, 16650.00, 11.00, null, 'Selang infus', 'Set penyaluran cairan intravena'],
            ['3014', 'SURSHIELD SURFLO II SAFETY NO 22 25', '4002',  'pcs',    '5003', 'Alkes',                                    'ALKES',                 35000.00, 38850.00, 11.00, null, 'IV Catheter Safety', 'Jarum infus pengaman'],
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
                    'ppn_percentage'              => $med[9],
                    'active_ingredient'           => $med[10],
                    'detailed_composition'        => $med[11],
                    'indication'                  => $med[12],
                    'is_active'                   => true,
                    'created_at'                  => now(),
                    'updated_at'                  => now(),
                ]
            );
        }

        // 6. Tarif per kelas kamar (dari tabel RS Azra, unit price per item)
        // Format: [item_code, SUITES, VVIP, VIP, UTAMA, KELAS_I, KELAS_II, KELAS_III]
        $tariffs = [
            ['3001', 440877, 426181, 426181, 411485, 411485, 411485, 411485],
            ['3002', 306054, 295852, 295852, 285650, 285650, 285650, 285650],
            ['3003',  36029,  66728,  66728,  64427,  64427,  64427,  64427],
            ['3004',  49950,  48285,  48285,  46620,  46620,  46620,  46620],
            ['3005',  18870,  18241,  18241,  17612,  17612,  17612,  17612],
            ['3006',  28305,  27362,  27362,  26418,  26418,  26418,  26418],
            ['3007',  31386,  29534,  29534,  28516,  28516,  28516,  28516],
            ['3008',  13320,  12876,  12876,  12432,  12432,  12432,  12432],
            ['3009',   9658,   8369,   8369,   8081,   8081,   8081,   8081],
            ['3010',     804,    777,    777,    750,    750,    750,    750],
        ];

        $classCodes = ['1', '2', '3', '4', '5', '6', '7'];

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
