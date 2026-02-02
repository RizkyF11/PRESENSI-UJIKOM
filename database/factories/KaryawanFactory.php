<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class KaryawanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nip' => $this->faker->unique()->numerify('EMP###'),
            'jabatan' => $this->faker->jobTitle(),
            'no_hp' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'status' => 'aktif',
        ];
    }
}
