<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Karyawan,
    Shift,
    QrCode,
    Absensi,
    Izin,
    Cuti
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // KaryawanSeeder::class,
            // ShiftSeeder::class,
            // QrCodeSeeder::class,
            // AbsensiSeeder::class,
            // IzinSeeder::class,
            // CutiSeeder::class,
        ]);
    }
}
