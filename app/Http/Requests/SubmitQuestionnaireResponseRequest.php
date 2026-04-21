<?php

namespace App\Http\Requests;

use App\Concerns\NormalizesAnswers;
use App\Models\OrganizationQuestionnaire;
use App\Models\QuestionnaireQuestion;
use App\Models\User;
use App\Support\Questionnaires\AvailableQuestionnaireCatalog;
use App\Support\Questionnaires\LocalizedQuestionnaireContent;
use App\Support\Questionnaires\QuestionnaireConditionEvaluator;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SubmitQuestionnaireResponseRequest extends FormRequest
{
    use NormalizesAnswers;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'intent' => ['required', 'string', Rule::in(['draft', 'submit', 'autosave'])],
            'answers' => ['nullable', 'array'],
            'current_category_id' => ['nullable', 'integer'],
        ];
    }

    public function saveAsDraft(): bool
    {
        return $this->string('intent')->value() !== 'submit';
    }

    public function isAutosave(): bool
    {
        return $this->string('intent')->value() === 'autosave';
    }

    /**
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->validateAnswers($validator);
            },
        ];
    }

    private function validateAnswers(Validator $validator): void
    {
        /** @var OrganizationQuestionnaire $organizationQuestionnaire */
        $organizationQuestionnaire = $this->route('organizationQuestionnaire');
        /** @var User $user */
        $user = $this->user();
        $localeContext = app(AvailableQuestionnaireCatalog::class)->localeContext($this, $user);
        $questionnaire = app(LocalizedQuestionnaireContent::class)->apply(
            $organizationQuestionnaire->questionnaire,
            $localeContext['locale'],
        );
        $questions = $questionnaire->categories->flatMap->questions;
        $answers = $this->input('answers', []);
        $conditionEvaluator = app(QuestionnaireConditionEvaluator::class);
        $visibleQuestions = collect($conditionEvaluator->visibleQuestions($questions, $answers))
            ->keyBy('id');

        foreach ($questions as $question) {
            if (! $visibleQuestions->has($question->id)) {
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

            if ($this->isAutosave()) {
                continue;
            }

            $this->validateAnswerValue($question, $value, $validator);
        }
    }

    protected function validateAnswerValue(
        QuestionnaireQuestion $question,
        mixed $value,
        Validator $validator
    ): void {
        $key = "answers.{$question->id}";

        if (in_array($question->type, [
            QuestionnaireQuestion::TYPE_SHORT_TEXT,
            QuestionnaireQuestion::TYPE_LONG_TEXT,
            QuestionnaireQuestion::TYPE_DATE,
            QuestionnaireQuestion::TYPE_NUMBER,
            QuestionnaireQuestion::TYPE_BOOLEAN,
            QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
            QuestionnaireQuestion::TYPE_LIKERT_SCALE,
        ], true) && is_array($value)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.invalid_format'));

            return;
        }

        if ($question->type === QuestionnaireQuestion::TYPE_NUMBER && ! is_numeric($value)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.number'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_DATE && ! Carbon::canBeCreatedFromFormat((string) $value, 'Y-m-d')) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.date'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_BOOLEAN && ! in_array((string) $value, ['0', '1'], true)) {
            $validator->errors()->add($key, __('hermes.questionnaire.validation.boolean'));
        }

        if ($question->type === QuestionnaireQuestion::TYPE_SINGLE_CHOICE
            || $question->type === QuestionnaireQuestion::TYPE_LIKERT_SCALE) {
            if (! in_array((string) $value, $question->options ?? [], true)) {
                $validator->errors()->add($key, __('hermes.questionnaire.validation.single_choice'));
            }
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
