<?php

namespace App\Http\Requests;

use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireQuestion;
use App\Support\Questionnaires\QuestionnaireConditionEvaluator;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitQuestionnaireResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'intent' => ['required', 'string', Rule::in(['draft', 'submit'])],
            'answers' => ['nullable', 'array'],
        ];
    }

    public function saveAsDraft(): bool
    {
        return $this->string('intent')->value() === 'draft';
    }

    /**
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                /** @var OrganizationQuestionnaire $organizationQuestionnaire */
                $organizationQuestionnaire = $this->route('organizationQuestionnaire');
                $questions = $organizationQuestionnaire->questionnaire
                    ->loadMissing('categories.questions')
                    ->questions;
                $answers = $this->input('answers', []);
                $conditionEvaluator = app(QuestionnaireConditionEvaluator::class);

                foreach ($questions as $question) {
                    if (! $conditionEvaluator->isVisible($question, $answers)) {
                        continue;
                    }

                    $value = data_get($answers, (string) $question->id);

                    if (! $this->saveAsDraft() && $question->is_required && $this->isEmptyAnswer($value)) {
                        $validator->errors()->add("answers.{$question->id}", __('hermes.questionnaire.validation.required'));

                        continue;
                    }

                    if ($this->isEmptyAnswer($value)) {
                        continue;
                    }

                    $this->validateAnswerValue($question, $value, $validator);
                }
            },
        ];
    }

    protected function isEmptyAnswer(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->filter(fn ($item): bool => trim((string) $item) !== '')->isEmpty();
        }

        return trim((string) $value) === '';
    }

    protected function validateAnswerValue(
        QuestionnaireQuestion $question,
        mixed $value,
        mixed $validator
    ): void {
        $key = "answers.{$question->id}";

        if (in_array($question->type, [
            QuestionnaireQuestion::TYPE_SHORT_TEXT,
            QuestionnaireQuestion::TYPE_LONG_TEXT,
            QuestionnaireQuestion::TYPE_DATE,
            QuestionnaireQuestion::TYPE_NUMBER,
            QuestionnaireQuestion::TYPE_BOOLEAN,
            QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
        ], true) && is_array($value)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.invalid_format'));

            return;
        }

        if ($question->type === QuestionnaireQuestion::TYPE_NUMBER && ! is_numeric($value)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.number'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_DATE && strtotime((string) $value) === false) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.date'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_BOOLEAN && ! in_array((string) $value, ['0', '1'], true)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.boolean'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_SINGLE_CHOICE
            && ! in_array((string) $value, $question->options ?? [], true)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.single_choice'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE) {
            if (! is_array($value)) {
                $validator->errors()->add($key, __('hermes.questionnaire.validation.multiple_choice'));

                return;
            }

            $invalidOptions = collect($value)
                ->filter(fn ($item): bool => ! in_array((string) $item, $question->options ?? [], true));

            if ($invalidOptions->isNotEmpty()) {
                $validator->errors()->add($key, __('hermes.questionnaire.validation.multiple_choice_invalid'));
            }
        }
    }
}
