<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $activeTag = trim((string) $request->string('tag'));
        $locale = app()->getLocale();

        $blogPostsQuery = BlogPost::query()
            ->with('author')
            ->published()
            ->when($search !== '', function ($query) use ($search, $locale): void {
                $query->where(function ($blogPostQuery) use ($search, $locale): void {
                    $blogPostQuery
                        ->where("title->{$locale}", 'like', "%{$search}%")
                        ->orWhere("excerpt->{$locale}", 'like', "%{$search}%")
                        ->orWhere("content->{$locale}", 'like', "%{$search}%")
                        ->orWhereJsonContains('tags', $search);
                });
            })
            ->when($activeTag !== '', fn ($query) => $query->whereJsonContains('tags', $activeTag));

        $featuredPost = (clone $blogPostsQuery)
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->first();

        $blogPosts = (clone $blogPostsQuery)
            ->when($featuredPost !== null, fn ($query) => $query->whereKeyNot($featuredPost->getKey()))
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('blog.index', [
            'featuredPost' => $featuredPost,
            'blogPosts' => $blogPosts,
            'search' => $search,
            'activeTag' => $activeTag,
            'tagCounts' => $this->tagCounts(),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless($blogPost->isPublished(), 404);

        $relatedPosts = BlogPost::query()
            ->with('author')
            ->published()
            ->whereKeyNot($blogPost->getKey())
            ->when($blogPost->tagsList() !== [], function ($query) use ($blogPost): void {
                $query->where(function ($relatedQuery) use ($blogPost): void {
                    foreach ($blogPost->tagsList() as $tag) {
                        $relatedQuery->orWhereJsonContains('tags', $tag);
                    }
                });
            })
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('blog.show', [
            'blogPost' => $blogPost->load('author'),
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * @return Collection<string, int>
     */
    protected function tagCounts(): Collection
    {
        return BlogPost::query()
            ->published()
            ->get(['tags'])
            ->flatMap(fn (BlogPost $blogPost): array => $blogPost->tagsList())
            ->countBy()
            ->sortDesc();
    }
}
