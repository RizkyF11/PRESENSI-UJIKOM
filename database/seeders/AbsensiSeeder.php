<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Karyawan, Shift, QrCode, Absensi};

class AbsensiSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan shift & qr sudah ada
        $shift = Shift::first() ?? Shift::factory()->create();
        $qr = QrCode::first() ?? QrCode::factory()->create([
            'is_active' => true,
        ]);

        Karyawan::all()->each(function ($karyawan) use ($shift, $qr) {
            Absensi::factory()->create([
                'karyawan_id' => $karyawan->id,
                'shift_id' => $shift->id,
                'qr_code_id' => $qr->id,
                'tanggal' => today(),
                'status' => 'hadir',
            ]);
        });
    }
}
