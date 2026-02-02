<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShiftFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_shift' => $this->faker->randomElement(['Pagi', 'Siang', 'Malam']),
            'jam_masuk' => '08:00:00',
            'jam_keluar' => '16:00:00',
            'toleransi_menit' => 10,
        ];
    }
}
