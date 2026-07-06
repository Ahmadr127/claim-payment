<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitCostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get SIRS organization unit
        $sirs = DB::table('organization_units')->where('code', 'SIRS')->first();
        
        if (! $sirs) {
            return;
        }

        // Get admin user
        $admin = DB::table('users')->first();

        // Get some diagnoses to assign
        $diagnoses = DB::table('diagnoses')
            ->limit(5)
            ->get();

        foreach ($diagnoses as $diagnosis) {
            DB::table('unit_cost_assignments')->updateOrInsert(
                [
                    'diagnosis_id' => $diagnosis->id,
                    'organization_unit_id' => $sirs->id,
                ],
                [
                    'is_active' => true,
                    'notes' => 'Initial assignment',
                    'assigned_by' => $admin?->id,
                    'assigned_at' => now(),
                    'is_customized' => false,
                    'customized_data' => null,
                    'customized_at' => null,
                    'customized_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
