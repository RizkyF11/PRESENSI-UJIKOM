<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin;
use App\Models\Karyawan;
use App\Models\Absensi;

class IzinSeeder extends Seeder
{
    public function run(): void
    {
        Karyawan::all()->each(function ($karyawan) {
            $absensi = Absensi::factory()->create([
                'karyawan_id' => $karyawan->id,
                'tanggal' => today(),
            ]);

            Izin::factory()->create([
                'karyawan_id' => $karyawan->id,
                'absensi_id' => $absensi->id,
            ]);
        });
    }
}

