<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QrCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode' => $this->faker->uuid(),
            'is_active' => true,
            'expired_at' => now()->addMinutes(5),
        ];
    }
}
