<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

class CutiFactory extends Factory
{
    public function definition(): array
    {
        $mulai = now()->subDays(rand(1, 30));

        return [
            'karyawan_id'     => Karyawan::factory(),
            'tanggal_mulai'   => $mulai->toDateString(),
            'tanggal_selesai' => (clone $mulai)->addDays(rand(2, 7))->toDateString(),
            'alasan'          => $this->faker->randomElement([
                'Cuti Tahunan',
                'Cuti Melahirkan',
                'Cuti Sakit',
                'Keperluan Keluarga',
            ]),
            'status'          => 'pending',
        ];
    }
}