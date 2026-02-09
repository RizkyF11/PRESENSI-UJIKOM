<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * =========================
         * ADMIN
         * =========================
         */
        User::factory()->create([
            'nama' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        /**
         * =========================
         * KARYAWAN (DUMMY)
         * =========================
         */
        User::factory(10)->create([
            'role' => 'karyawan',
        ]);
    }
}
