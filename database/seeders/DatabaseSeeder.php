<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Master data dulu
            UserSeeder::class,
            ShiftSeeder::class,
            LokasiKantorSeeder::class,
            QrCodeSeeder::class,

            // 2. Data karyawan (butuh user)
            KaryawanSeeder::class,

            // 3. Relasi karyawan ke shift
            KaryawanShiftSeeder::class,

            // 4. Data transaksi (butuh karyawan, shift, lokasi, qr)
            AbsensiSeeder::class,
            IzinSeeder::class,
            CutiSeeder::class,
        ]);
    }
}