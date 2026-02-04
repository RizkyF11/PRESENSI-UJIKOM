<?php

namespace Database\Factories;

use App\Models\{Karyawan, Absensi};
use Illuminate\Database\Eloquent\Factories\Factory;

class IzinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'karyawan_id' => Karyawan::factory(),
            'absensi_id' => null, // diisi di seeder
            'tanggal' => today(),
            'alasan' => 'Sakit',
            'status' => 'pending',
        ];
    }
}

