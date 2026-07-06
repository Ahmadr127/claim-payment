<?php

namespace Database\Seeders;

use App\Models\ClinicalPathway\Diagnosis;
use App\Models\OrganizationUnit;
use App\Models\UnitCost\UnitCostAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class UnitCostAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some diagnoses with pathways
        $diagnoses = Diagnosis::whereHas('pathway')->limit(5)->get();
        
        // Get some organization units
        $units = OrganizationUnit::limit(3)->get();

        // Get first user
        $admin = User::first();

        // Assign admin to first organization unit for testing
        if ($admin && $units->count() > 0) {
            $admin->update(['organization_unit_id' => $units->first()->id]);
        }

        foreach ($units as $unit) {
            // Assign 2-3 diagnoses ke setiap unit
            $assignedDiagnoses = $diagnoses->random(min(3, count($diagnoses)));
            
            foreach ($assignedDiagnoses as $diagnosis) {
                UnitCostAssignment::firstOrCreate(
                    [
                        'diagnosis_id' => $diagnosis->id,
                        'organization_unit_id' => $unit->id,
                    ],
                    [
                        'is_active' => true,
                        'notes' => 'Diagnosis ditugaskan untuk simulasi unit cost ' . $unit->name,
                        'assigned_by' => $admin?->id,
                        'assigned_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('UnitCostAssignment seeder completed.');
    }
}
