<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama'        => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'urutan'      => $this->faker->numberBetween(1, 10),
            'is_active'   => true,
        ];
    }
}