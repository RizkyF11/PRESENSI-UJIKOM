<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Shift;
use App\Models\LokasiKantor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsensiFactory extends Factory
{
    public function definition(): array
    {
        $isLate      = $this->faker->boolean(10); // 10% terlambat
        $masukHour   = $isLate ? 8 : 7;
        $masukMinute = $isLate ? rand(16, 59) : rand(30, 59);

        $baseLat = -6.200000;
        $baseLng = 106.816666;

        return [
            'karyawan_id'      => Karyawan::factory(),
            'shift_id'         => Shift::factory(),
            'lokasi_kantor_id' => null,
            'qr_code_id'       => null,
            'tanggal'          => today(),
            'jam_masuk'        => sprintf('%02d:%02d:00', $masukHour, $masukMinute),
            'jam_keluar'       => sprintf('17:%02d:00', rand(0, 30)),
            'latitude_masuk'   => $baseLat + (rand(-100, 100) / 1000000),
            'longitude_masuk'  => $baseLng + (rand(-100, 100) / 1000000),
            'latitude_keluar'  => $baseLat + (rand(-100, 100) / 1000000),
            'longitude_keluar' => $baseLng + (rand(-100, 100) / 1000000),
            'status_masuk'     => $isLate ? 'terlambat' : 'hadir',
            'status_keluar'    => 'pulang',
        ];
    }
}