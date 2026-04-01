<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $blogPosts = BlogPost::query()
            ->with('author')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.blog-posts.index', [
            'blogPosts' => $blogPosts,
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.form', [
            'title' => 'Nieuwe blogpost',
            'intro' => 'Publiceer een nieuw artikel voor de publieke Hermes Results blog.',
            'submitLabel' => 'Blogpost opslaan',
            'blogPost' => new BlogPost([
                'is_published' => true,
                'is_featured' => false,
                'published_at' => now(),
            ]),
            'isEditing' => false,
            'supportedLocales' => config('locales.supported', []),
        ]);
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        /** @var User $actor */
        $actor = $request->user();

        $blogPost = BlogPost::query()->create($this->blogPostPayload($request, $actor));

        return redirect()
            ->route('admin.blog-posts.edit', $blogPost)
            ->with('status', 'Blogpost succesvol toegevoegd.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.form', [
            'title' => 'Blogpost wijzigen',
            'intro' => 'Werk inhoud, metadata en publicatie-instellingen van deze blogpost bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'blogPost' => $blogPost,
            'isEditing' => true,
            'supportedLocales' => config('locales.supported', []),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update($this->blogPostPayload($request, $blogPost->author));

        return redirect()
            ->route('admin.blog-posts.edit', $blogPost)
            ->with('status', 'Blogpost succesvol bijgewerkt.');
    }

    public function confirmDestroy(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.confirm-delete', [
            'blogPost' => $blogPost,
        ]);
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'Blogpost succesvol verwijderd.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function blogPostPayload(Request $request, ?User $author = null): array
    {
        $attributes = $request->validated();
        $isPublished = $request->boolean('is_published');

        return [
            'author_id' => $author?->id,
            'slug' => $attributes['slug'],
            'cover_image_url' => $attributes['cover_image_url'] ?: null,
            'tags' => $this->tagPayload($attributes['tags'] ?? ''),
            'title' => $this->localizedFieldPayload($attributes['title']),
            'excerpt' => $this->localizedFieldPayload($attributes['excerpt']),
            'content' => $this->localizedFieldPayload($attributes['content']),
            'is_published' => $isPublished,
            'is_featured' => $isPublished && $request->boolean('is_featured'),
            'published_at' => $isPublished
                ? (($attributes['published_at'] ?? null) ? Carbon::parse($attributes['published_at']) : now())
                : null,
        ];
    }

    /**
     * @param  array<string, string>  $values
     * @return array<string, string>
     */
    protected function localizedFieldPayload(array $values): array
    {
        return collect($values)
            ->map(fn (string $value): string => trim($value))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function tagPayload(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value) ?: [])
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
