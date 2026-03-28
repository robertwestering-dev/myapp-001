<?php

namespace Database\Factories;

use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionnaireQuestion>
 */
class QuestionnaireQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_category_id' => QuestionnaireCategory::factory(),
            'prompt' => fake()->sentence().'?',
            'help_text' => fake()->optional()->sentence(),
            'type' => QuestionnaireQuestion::TYPE_SHORT_TEXT,
            'options' => null,
            'is_required' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    public function singleChoice(): static
    {
        return $this->state(fn () => [
            'type' => QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
            'options' => ['Altijd', 'Soms', 'Nooit'],
        ]);
    }
}
