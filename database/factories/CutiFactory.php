<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CutiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tanggal_mulai' => now()->addDays(rand(1, 5)),
            'tanggal_selesai' => now()->addDays(rand(6, 10)),
            'alasan' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'disetujui', 'ditolak']),
        ];
    }
}
