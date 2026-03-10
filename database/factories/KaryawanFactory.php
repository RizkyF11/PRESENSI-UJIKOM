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
            'nip'     => 'EMP' . $this->faker->unique()->numerify('####'),
            'jabatan' => $this->faker->jobTitle(),
            'no_hp'   => $this->faker->numerify('08##########'),
            'alamat'  => $this->faker->address(),
            'status'  => 'aktif',
        ];
    }
}