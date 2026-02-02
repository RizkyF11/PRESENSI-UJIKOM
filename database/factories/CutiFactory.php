<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

class CutiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'karyawan_id' => Karyawan::factory(),
            'tanggal_mulai' => now()->addDays(1),
            'tanggal_selesai' => now()->addDays(3),
            'alasan' => 'Cuti tahunan',
            'status' => 'disetujui',
        ];
    }
}
