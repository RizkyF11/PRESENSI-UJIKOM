<?php

namespace Database\Factories;

use App\Models\AssessmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentQuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => AssessmentCategory::factory(),
            'question'    => $this->faker->sentence(8) . '?',
            'urutan'      => $this->faker->numberBetween(1, 10),
            'is_active'   => true,
        ];
    }
}