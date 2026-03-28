<?php

namespace App\Http\Requests\Admin;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionnaireRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', Rule::unique(Questionnaire::class, 'title')->ignore($questionnaire)],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
