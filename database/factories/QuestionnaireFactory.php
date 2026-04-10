<?php

namespace Database\Factories;

use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Questionnaire>
 */
class QuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Questionnaire '.fake()->unique()->company(),
            'description' => fake()->sentence(),
            'locale' => config('app.locale'),
            'is_active' => true,
        ];
    }
}
