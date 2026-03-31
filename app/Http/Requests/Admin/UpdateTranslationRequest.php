<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', Rule::in(array_keys(config('locales.supported', [])))],
            'key' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'filter_locale' => ['nullable', 'string'],
            'filter_page' => ['nullable', 'string'],
            'filter_element' => ['nullable', 'string'],
            'filter_search' => ['nullable', 'string'],
            'page_number' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
