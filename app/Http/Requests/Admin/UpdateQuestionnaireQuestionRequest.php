<?php

namespace App\Http\Requests\Admin;

use App\Models\Questionnaire;
use App\Models\QuestionnaireCategory;
use App\Models\QuestionnaireQuestion;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionnaireQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Questionnaire $questionnaire */
        $questionnaire = $this->route('questionnaire');

        return [
            'questionnaire_category_id' => [
                'required',
                Rule::exists(QuestionnaireCategory::class, 'id')->where('questionnaire_id', $questionnaire->id),
            ],
            'prompt' => ['required', 'string'],
            'help_text' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(QuestionnaireQuestion::types())],
            'options' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                if (! in_array($this->input('type'), [
                    QuestionnaireQuestion::TYPE_SINGLE_CHOICE,
                    QuestionnaireQuestion::TYPE_MULTIPLE_CHOICE,
                ], true)) {
                    return;
                }

                $options = collect(preg_split('/\r\n|\r|\n/', (string) $this->input('options')))
                    ->map(fn (?string $option): string => trim((string) $option))
                    ->filter()
                    ->values();

                if ($options->count() < 2) {
                    $validator->errors()->add('options', 'Geef minimaal twee antwoordopties op, ieder op een nieuwe regel.');
                }
            },
        ];
    }
}
