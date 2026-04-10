<?php

namespace App\Http\Requests\Admin;

use App\Models\AcademyCourse;
use Illuminate\Validation\Rule;

class UpdateAcademyCourseRequest extends BaseLocalizedRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        /** @var AcademyCourse $academyCourse */
        $academyCourse = $this->route('academyCourse');

        return [
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique(AcademyCourse::class, 'slug')->ignore($academyCourse)],
            'theme' => ['required', 'string', Rule::in(array_keys(AcademyCourse::themes()))],
            'path' => ['required', 'string', 'max:255', Rule::unique(AcademyCourse::class, 'path')->ignore($academyCourse)],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
            ...$this->localizedStringRules('title', 255, primaryOnly: false),
            ...$this->localizedStringRules('audience', primaryOnly: false),
            ...$this->localizedStringRules('goal', primaryOnly: false),
            ...$this->localizedStringRules('summary', primaryOnly: false),
            ...$this->localizedStringRules('learning_goals', primaryOnly: false),
            ...$this->localizedStringRules('contents', primaryOnly: false),
        ];
    }
}
