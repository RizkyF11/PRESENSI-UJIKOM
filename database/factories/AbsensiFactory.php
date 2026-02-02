<?php

namespace Database\Factories;

use App\Models\{Karyawan, Shift, QrCode};
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'karyawan_id' => Karyawan::factory(),
            'shift_id' => Shift::factory(),
            'qr_code_id' => QrCode::factory(),
            'tanggal' => today(),
            'jam_masuk' => '08:05:00',
            'jam_keluar' => '16:00:00',
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'status' => 'hadir',
        ];
    }
}
