<?php

namespace Database\Factories;

use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireResponseAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionnaireResponseAnswer>
 */
class QuestionnaireResponseAnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'questionnaire_response_id' => QuestionnaireResponse::factory(),
            'questionnaire_question_id' => QuestionnaireQuestion::factory(),
            'answer' => fake()->sentence(),
            'answer_list' => null,
        ];
    }
}
