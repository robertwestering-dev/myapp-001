<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesLocalizedPayload;
use App\Enums\AuditAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use App\Models\BlogPost;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BlogPostController extends Controller
{
    use HandlesLocalizedPayload;

    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): View
    {
        $this->authorize('manage', BlogPost::class);

        $blogPosts = BlogPost::query()
            ->with('author')
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(config('app.per_page'));

        return view('admin.blog-posts.index', [
            'blogPosts' => $blogPosts,
        ]);
    }

    public function create(): View
    {
        $this->authorize('manage', BlogPost::class);

        return view('admin.blog-posts.form', [
            'title' => __('hermes.admin.form_titles.new_blog_post'),
            'intro' => 'Publiceer een nieuw artikel voor de publieke Hermes Results blog.',
            'submitLabel' => 'Blogpost opslaan',
            'blogPost' => new BlogPost([
                'is_published' => false,
                'is_featured' => false,
                'published_at' => now()->addDay(),
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

        $this->audit->log(AuditAction::BlogPostCreated, "Blogpost aangemaakt: {$blogPost->slug}", $blogPost);

        return redirect()
            ->route('admin.blog-posts.edit', $blogPost)
            ->with('status', __('hermes.admin.blog_posts.created'));
    }

    public function edit(BlogPost $blogPost): View
    {
        $this->authorize('manage', BlogPost::class);

        return view('admin.blog-posts.form', [
            'title' => __('hermes.admin.form_titles.edit_blog_post'),
            'intro' => 'Werk inhoud, metadata en publicatie-instellingen van deze blogpost bij.',
            'submitLabel' => 'Wijzigingen opslaan',
            'blogPost' => $blogPost,
            'isEditing' => true,
            'supportedLocales' => config('locales.supported', []),
        ]);
    }

    public function preview(BlogPost $blogPost): View
    {
        $this->authorize('manage', BlogPost::class);

        return view('blog.show', [
            'blogPost' => $blogPost->load('author'),
            'blogIndexUrl' => route('admin.blog-posts.edit', $blogPost),
            'relatedPosts' => collect(),
            'isPreview' => true,
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $wasPublished = $blogPost->is_published;
        $blogPost->update($this->blogPostPayload($request, $blogPost->author));

        $this->audit->log(AuditAction::BlogPostUpdated, "Blogpost bijgewerkt: {$blogPost->slug}", $blogPost);

        if (! $wasPublished && $blogPost->is_published) {
            $this->audit->log(AuditAction::BlogPostPublished, "Blogpost gepubliceerd: {$blogPost->slug}", $blogPost);
        }

        return redirect()
            ->route('admin.blog-posts.edit', $blogPost)
            ->with('status', __('hermes.admin.blog_posts.updated'));
    }

    public function confirmDestroy(BlogPost $blogPost): View
    {
        $this->authorize('manage', BlogPost::class);

        return view('admin.blog-posts.confirm-delete', [
            'blogPost' => $blogPost,
        ]);
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $this->authorize('manage', BlogPost::class);

        $this->audit->log(AuditAction::BlogPostDeleted, "Blogpost verwijderd: {$blogPost->slug}", $blogPost);

        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', __('hermes.admin.blog_posts.deleted'));
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
            'cover_image_url' => ($attributes['cover_image_url'] ?? '') ?: null,
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
