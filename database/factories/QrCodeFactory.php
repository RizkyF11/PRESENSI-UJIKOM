<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class QrCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kode'       => Str::uuid(),
            'is_active'  => true,
            'expired_at' => now()->addMinutes(5),
        ];
    }
}