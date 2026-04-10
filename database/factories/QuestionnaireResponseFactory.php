<?php

namespace Database\Factories;

use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionnaireResponse>
 */
class QuestionnaireResponseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_questionnaire_id' => OrganizationQuestionnaire::factory(),
            'user_id' => User::factory(),
            'submitted_at' => now(),
            'last_saved_at' => now(),
            'resume_token' => fake()->unique()->sha256(),
            'current_questionnaire_category_id' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'submitted_at' => null,
            'last_saved_at' => now(),
        ]);
    }
}
