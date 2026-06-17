<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Auth & Org
            RolePermissionSeeder::class,
            OrganizationTypeSeeder::class,
            OrganizationUnitSeeder::class,

            // Master data — urutan penting: room_classes dulu sebelum service/medication
            RoomClassSeeder::class,
            ClaimStatusSeeder::class,
            ServiceCategorySeeder::class,
            MedicationSeeder::class,
            DiagnosisPathwaySeeder::class,
        ]);

        // Clear existing users to only leave what we seed
        \App\Models\User::truncate();

        // Create admin user
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        
        User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('rsazra'),
            'role_id' => $adminRole->id,
        ]);

        // Create master data user
        $masterDataRole = \App\Models\Role::where('name', 'master_data')->first();
        
        User::factory()->create([
            'name' => 'Staff Master Data',
            'username' => 'staff',
            'email' => 'staff@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('rsazra'),
            'role_id' => $masterDataRole->id,
        ]);
    }
}
