<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Auth & Akses
            RolePermissionSeeder::class,

            // Organisasi
            OrganizationTypeSeeder::class,
            OrganizationUnitSeeder::class,

            // Master data — urutan penting: room_classes dulu sebelum service/medication
            RoomClassSeeder::class,
            ServiceGroupSeeder::class,
            MedicationSeeder::class,
            DiagnosisPathwaySeeder::class,

            // Users (terakhir agar role sudah ada)
            UserSeeder::class,
        ]);
    }
}
