<?php

namespace App\Http\Requests\Admin;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique(Questionnaire::class, 'title')],
            'description' => ['nullable', 'string'],
            'locale' => ['required', 'string', Rule::in(Questionnaire::localeOptions())],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
