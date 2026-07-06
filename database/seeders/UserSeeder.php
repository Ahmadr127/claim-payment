<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // TRUNCATE CASCADE untuk PostgreSQL (menghapus records di tabel yang referensi users)
        DB::statement('TRUNCATE TABLE users RESTART IDENTITY CASCADE');

        $adminRole      = Role::where('name', 'admin')->first();
        $masterDataRole = Role::where('name', 'master_data')->first();
        $userRole       = Role::where('name', 'user')->first();

        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('rsazra'),
            'role_id'  => $adminRole->id,
        ]);

        User::create([
            'name'     => 'Staff Master Data',
            'username' => 'staff',
            'email'    => 'staff@example.com',
            'password' => Hash::make('rsazra'),
            'role_id'  => $masterDataRole->id,
        ]);

        User::create([
            'name'     => 'Pengguna Umum',
            'username' => 'user',
            'email'    => 'user@example.com',
            'password' => Hash::make('rsazra'),
            'role_id'  => $userRole->id,
        ]);
    }
}

