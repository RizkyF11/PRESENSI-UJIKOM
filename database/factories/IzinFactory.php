<?php

namespace Database\Factories;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

class IzinFactory extends Factory
{
    public function definition(): array
    {
        $mulai = now()->subDays(rand(1, 30));

        return [
            'karyawan_id'     => Karyawan::factory(),
            'tanggal_mulai'   => $mulai->toDateString(),
            'tanggal_selesai' => (clone $mulai)->addDays(rand(1, 3))->toDateString(),
            'alasan'          => $this->faker->randomElement([
                'Sakit',
                'Keperluan Keluarga',
                'Urusan Pribadi',
                'Pemeriksaan Dokter',
            ]),
            'status'          => 'pending',
        ];
    }
}