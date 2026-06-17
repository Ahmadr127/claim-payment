<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationType;
use App\Models\OrganizationUnit;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizationUnitSeeder extends Seeder
{
    public function run(): void
    {
        // Get types
        $holdingType = OrganizationType::where('name', 'holding')->first();
        $hospitalType = OrganizationType::where('name', 'hospital')->first();
        $directorateType = OrganizationType::where('name', 'directorate')->first();
        $departmentType = OrganizationType::where('name', 'department')->first();
        $unitType = OrganizationType::where('name', 'unit')->first();

        // Get or create manager role
        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['display_name' => 'Manager', 'description' => 'Manager unit organisasi']
        );
        
        $staffRole = Role::firstOrCreate(
            ['name' => 'staff'],
            ['display_name' => 'Staff', 'description' => 'Staff umum']
        );

        // ========================================
        // 1. DIREKTUR UTAMA (Top Level)
        // ========================================
        $direkturUtama = OrganizationUnit::create([
            'name' => 'Direktur Utama',
            'code' => 'DIRUT',
            'type_id' => $holdingType->id,
            'parent_id' => null,
            'description' => 'Direktur Utama Rumah Sakit',
            'is_active' => true,
        ]);

        // ========================================
        // 2. DEPARTEMEN SIRS (langsung dibawah Direktur Utama)
        // ========================================
        $sirs = OrganizationUnit::create([
            'name' => 'Departemen SIRS',
            'code' => 'SIRS',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama->id,
            'description' => 'Sistem Informasi Rumah Sakit',
            'is_active' => true,
        ]);

        // ========================================
        // 3. SEKRETARIS (langsung dibawah Direktur Utama)
        // ========================================
        $sekretaris = OrganizationUnit::create([
            'name' => 'Sekretaris',
            'code' => 'SEKR',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama->id,
            'description' => 'Sekretaris Direktur',
            'is_active' => true,
        ]);

        // ========================================
        // 4. KEPERAWATAN (langsung dibawah Direktur Utama)
        // ========================================
        $keperawatan = OrganizationUnit::create([
            'name' => 'Departemen Keperawatan',
            'code' => 'PERAWAT',
            'type_id' => $departmentType->id,
            'parent_id' => $direkturUtama->id,
            'description' => 'Departemen Keperawatan',
            'is_active' => true,
        ]);

        // ========================================
        // 5. SUB-UNIT KEPERAWATAN (untuk contoh hierarki lebih dalam)
        // ========================================
        
        // Unit Rawat Inap
        $rawatInap = OrganizationUnit::create([
            'name' => 'Unit Rawat Inap',
            'code' => 'RANAP',
            'type_id' => $unitType->id,
            'parent_id' => $keperawatan->id,
            'description' => 'Unit Rawat Inap Keperawatan',
            'is_active' => true,
        ]);

        // Unit IGD
        $igd = OrganizationUnit::create([
            'name' => 'Unit IGD',
            'code' => 'IGD',
            'type_id' => $unitType->id,
            'parent_id' => $keperawatan->id,
            'description' => 'Instalasi Gawat Darurat',
            'is_active' => true,
        ]);
    }
}
