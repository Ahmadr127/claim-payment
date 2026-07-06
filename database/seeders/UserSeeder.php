<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan updateOrCreate agar aman dijalankan berkali-kali
        // (TIDAK pakai TRUNCATE CASCADE karena akan ikut menghapus semua
        //  tabel yang punya FK created_by/updated_by → users)

        $adminRole      = Role::where('name', 'admin')->first();
        $masterDataRole = Role::where('name', 'master_data')->first();
        $userRole       = Role::where('name', 'user')->first();

        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@example.com',
                'password' => Hash::make('rsazra'),
                'role_id'  => $adminRole->id,
            ]
        );

        User::updateOrCreate(
            ['username' => 'staff'],
            [
                'name'     => 'Staff Master Data',
                'email'    => 'staff@example.com',
                'password' => Hash::make('rsazra'),
                'role_id'  => $masterDataRole->id,
            ]
        );

        User::updateOrCreate(
            ['username' => 'user'],
            [
                'name'     => 'Pengguna Umum',
                'email'    => 'user@example.com',
                'password' => Hash::make('rsazra'),
                'role_id'  => $userRole->id,
            ]
        );
    }
}
