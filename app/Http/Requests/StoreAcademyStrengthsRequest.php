<?php

namespace App\Http\Requests;

use App\Support\Academy\PositiveFoundationStrengthCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAcademyStrengthsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $allowedStrengths = app(PositiveFoundationStrengthCatalog::class)->keys();

        return [
            'selected_strengths' => ['required', 'array', 'size:3'],
            'selected_strengths.*' => ['required', 'string', 'distinct:strict', Rule::in($allowedStrengths)],
        ];
    }

    public function messages(): array
    {
        return [
            'selected_strengths.required' => __('hermes.academy.strengths_widget.validation.required'),
            'selected_strengths.array' => __('hermes.academy.strengths_widget.validation.required'),
            'selected_strengths.size' => __('hermes.academy.strengths_widget.validation.size'),
            'selected_strengths.*.required' => __('hermes.academy.strengths_widget.validation.required'),
            'selected_strengths.*.distinct' => __('hermes.academy.strengths_widget.validation.distinct'),
            'selected_strengths.*.in' => __('hermes.academy.strengths_widget.validation.invalid'),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function selectedStrengths(): array
    {
        return array_values(array_map(
            static fn (mixed $strength): string => (string) $strength,
            $this->validated('selected_strengths'),
        ));
    }
}
