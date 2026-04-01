<?php

namespace App\Http\Requests\Admin;

use App\Models\BlogPost;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var BlogPost $blogPost */
        $blogPost = $this->route('blogPost');

        return [
            'slug' => ['required', 'string', 'max:140', 'alpha_dash', Rule::unique(BlogPost::class, 'slug')->ignore($blogPost)],
            'cover_image_url' => ['nullable', 'url', 'max:2048'],
            'tags' => ['nullable', 'string', 'max:2000'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            ...$this->localizedStringRules('title', 255),
            ...$this->localizedStringRules('excerpt', 500),
            ...$this->localizedStringRules('content'),
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
