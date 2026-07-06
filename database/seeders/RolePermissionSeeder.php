<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Define all permissions
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'Lihat Dashboard', 'description' => 'Melihat halaman dashboard'],
            ['name' => 'manage_roles', 'display_name' => 'Kelola Roles', 'description' => 'Mengelola roles dan permissions'],
            ['name' => 'manage_users', 'display_name' => 'Kelola Users', 'description' => 'Mengelola pengguna'],
            ['name' => 'manage_organization_types', 'display_name' => 'Kelola Tipe Organisasi', 'description' => 'Mengelola tipe organisasi'],
            ['name' => 'manage_organization_units', 'display_name' => 'Kelola Unit Organisasi', 'description' => 'Mengelola unit organisasi'],
            ['name' => 'manage_service_groups', 'display_name' => 'Kelola Golongan Layanan', 'description' => 'Mengelola golongan layanan medis'],
            ['name' => 'manage_services', 'display_name' => 'Kelola Layanan Medis', 'description' => 'Mengelola layanan medis dan tarifnya'],
            ['name' => 'manage_clinical_pathway', 'display_name' => 'Kelola Tarif Umum', 'description' => 'Mengelola tarif umum per diagnosa'],
            ['name' => 'manage_medications', 'display_name' => 'Kelola Obat & Alkes', 'description' => 'Mengelola master data obat, alkes, dan tarifnya'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }

        // Create Roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Role dengan akses penuh ke sistem']
        );

        $userRole = Role::updateOrCreate(
            ['name' => 'user'],
            ['display_name' => 'Pengguna', 'description' => 'Role untuk pengguna umum']
        );

        $masterDataRole = Role::updateOrCreate(
            ['name' => 'master_data'],
            ['display_name' => 'Admin Master Data', 'description' => 'Role untuk mengelola master data layanan dan tarif']
        );

        // Assign permissions to roles
        $adminRole->permissions()->sync(Permission::all()); // Admin gets all permissions
        
        $userRole->permissions()->sync(
            Permission::whereIn('name', [
                'view_dashboard'
            ])->pluck('id')->toArray()
        );

        $masterDataRole->permissions()->sync(
            Permission::whereIn('name', [
                'view_dashboard',
                'manage_service_groups',
                'manage_services',
                'manage_clinical_pathway',
                'manage_medications'
            ])->pluck('id')->toArray()
        );
    }
}
