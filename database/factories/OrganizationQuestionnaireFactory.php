<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationQuestionnaire;
use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationQuestionnaire>
 */
class OrganizationQuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_id' => Questionnaire::factory(),
            'org_id' => Organization::factory(),
            'available_from' => now()->toDateString(),
            'available_until' => null,
            'is_active' => true,
        ];
    }
}
