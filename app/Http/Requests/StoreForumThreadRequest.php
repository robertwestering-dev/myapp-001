<?php

namespace App\Http\Requests;

use App\Models\ForumThread;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreForumThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'discussion_type' => ['required', 'string', Rule::in(ForumThread::discussionTypeOptions())],
            'title' => ['required', 'string', 'min:6', 'max:160'],
            'body' => ['required', 'string', 'min:20', 'max:10000'],
            'tags' => ['nullable', 'array', 'max:5'],
            'tags.*' => ['string', 'min:2', 'max:32'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $tags = collect(explode(',', (string) $this->input('tags')))
            ->map(fn (string $tag): string => trim(Str::limit($tag, 32, '')))
            ->filter()
            ->unique(fn (string $tag): string => Str::lower($tag))
            ->take(5)
            ->values()
            ->all();

        $this->merge([
            'tags' => $tags,
        ]);
    }
}
