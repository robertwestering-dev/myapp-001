<?php

namespace App\Http\Requests\Admin;

use App\Models\Questionnaire;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizationQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessAdminPortal() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    public function rules(): array
    {
        return [
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                /** @var Questionnaire|null $questionnaire */
                $questionnaire = $this->route('questionnaire');
                if ($questionnaire === null || ! $this->boolean('is_active') || $questionnaire->is_active) {
                    // continue with remaining validation rules
                } else {
                    $validator->errors()->add('is_active', __('hermes.questionnaires.availability_requires_active_questionnaire'));
                }
            },
        ];
    }
}
