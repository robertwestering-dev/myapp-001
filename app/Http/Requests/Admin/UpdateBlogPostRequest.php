<?php

namespace App\Http\Requests\Admin;

use App\Models\BlogPost;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends BaseLocalizedRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
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
}
