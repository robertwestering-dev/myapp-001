<?php

namespace App\Http\Requests\Admin;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'pro_only' => $this->boolean('pro_only'),
        ]);
    }

    public function rules(): array
    {
        /** @var Questionnaire $questionnaire */
        $questionnaire = $this->route('questionnaire');

        return [
            'title' => ['required', 'string', 'max:255', Rule::unique(Questionnaire::class, 'title')->ignore($questionnaire)],
            'description' => ['nullable', 'string'],
            'locale' => ['required', 'string', Rule::in(Questionnaire::localeOptions())],
            'is_active' => ['required', 'boolean'],
            'pro_only' => ['required', 'boolean'],
        ];
    }
}
