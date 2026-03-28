<?php

namespace App\Http\Requests\Admin;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationQuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User|null $actor */
        $actor = $this->user();

        $organizationRule = $actor?->isAdmin()
            ? Rule::exists(Organization::class, 'org_id')
            : Rule::in([(string) $actor?->org_id]);

        return [
            'org_id' => ['required', $organizationRule],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
