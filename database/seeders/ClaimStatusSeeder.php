<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seed status klaim dan aturan transisi antar status.
 * Aturan transisi disimpan di DB — bisa diubah admin tanpa deploy ulang.
 */
class ClaimStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'draft',     'name' => 'Draft',     'color' => 'gray',    'display_order' => 1, 'is_terminal' => false],
            ['code' => 'submitted', 'name' => 'Submitted', 'color' => 'blue',    'display_order' => 2, 'is_terminal' => false],
            ['code' => 'verified',  'name' => 'Verified',  'color' => 'yellow',  'display_order' => 3, 'is_terminal' => false],
            ['code' => 'approved',  'name' => 'Approved',  'color' => 'green',   'display_order' => 4, 'is_terminal' => false],
            ['code' => 'rejected',  'name' => 'Rejected',  'color' => 'red',     'display_order' => 5, 'is_terminal' => true],
            ['code' => 'paid',      'name' => 'Paid',      'color' => 'emerald', 'display_order' => 6, 'is_terminal' => true],
        ];

        foreach ($statuses as $status) {
            DB::table('claim_statuses')->updateOrInsert(
                ['code' => $status['code']],
                array_merge($status, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Helper closure untuk resolve ID status
        $statusId = fn(string $code) => DB::table('claim_statuses')->where('code', $code)->value('id');

        // Aturan transisi: dari → ke (+ role yang boleh)
        $transitions = [
            ['from' => 'draft',     'to' => 'submitted', 'required_role' => 'billing_staff'],
            ['from' => 'submitted', 'to' => 'verified',  'required_role' => 'verifikator'],
            ['from' => 'submitted', 'to' => 'rejected',  'required_role' => 'verifikator'],
            ['from' => 'verified',  'to' => 'approved',  'required_role' => 'approver'],
            ['from' => 'verified',  'to' => 'rejected',  'required_role' => 'approver'],
            ['from' => 'approved',  'to' => 'paid',      'required_role' => 'kasir'],
        ];

        foreach ($transitions as $t) {
            DB::table('claim_status_transitions')->updateOrInsert(
                [
                    'from_status_id' => $statusId($t['from']),
                    'to_status_id'   => $statusId($t['to']),
                ],
                [
                    'required_role' => $t['required_role'],
                    'is_active'     => true,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]
            );
        }
    }
}
