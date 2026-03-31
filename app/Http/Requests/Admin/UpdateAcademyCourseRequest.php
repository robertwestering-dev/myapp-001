<?php

namespace App\Http\Requests\Admin;

use App\Models\AcademyCourse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademyCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            ...$this->localizedStringRules('title', 255),
            ...$this->localizedStringRules('audience'),
            ...$this->localizedStringRules('goal'),
            ...$this->localizedStringRules('summary'),
            ...$this->localizedStringRules('learning_goals'),
            ...$this->localizedStringRules('contents'),
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function localizedStringRules(string $attribute, ?int $maxLength = null): array
    {
        $rules = [
            $attribute => ['required', 'array'],
        ];

        foreach (array_keys(config('locales.supported', [])) as $locale) {
            $fieldRules = ['required', 'string'];

            if ($maxLength !== null) {
                $fieldRules[] = 'max:'.$maxLength;
            }

            $rules["{$attribute}.{$locale}"] = $fieldRules;
        }

        return $rules;
    }
}
