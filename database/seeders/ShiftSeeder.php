<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'nama_shift' => 'Pagi',
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '16:00:00',
                'toleransi_menit' => 10,
            ],
            [
                'nama_shift' => 'Siang',
                'jam_masuk' => '12:00:00',
                'jam_keluar' => '20:00:00',
                'toleransi_menit' => 10,
            ],
            [
                'nama_shift' => 'Malam',
                'jam_masuk' => '00:00:00',
                'jam_keluar' => '00:30:00',
                'toleransi_menit' => 10,
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::updateOrCreate(
                ['nama_shift' => $shift['nama_shift']],
                $shift
            );
        }
    }
}
