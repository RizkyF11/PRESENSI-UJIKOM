<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama'     => 'Admin Utama',
                'role'     => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );

        // manager
        User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'nama'     => 'Manager',
                'role'     => 'manager',
                'password' => Hash::make('manager123'),
            ]
        );

        // 10 Karyawan
        for ($i = 1; $i <= 10; $i++) {
            User::updateOrCreate(
                ['email' => 'karyawan' . $i . '@gmail.com'],
                [
                    'nama'     => 'Karyawan ' . $i,
                    'role'     => 'karyawan',
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}