<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    private const RELATED_POSTS_LIMIT = 3;

    private const RELATED_CANDIDATES_LIMIT = 50;

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $activeTag = trim((string) $request->string('tag'));
        $locale = app()->getLocale();

        $blogPostsQuery = BlogPost::query()
            ->with('author')
            ->published()
            ->when($search !== '', function ($query) use ($search, $locale): void {
                // Escape wildcard characters to prevent unintended broad LIKE matches.
                $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);

                $query->where(function ($blogPostQuery) use ($escaped, $locale): void {
                    $blogPostQuery
                        ->where("title->{$locale}", 'like', "%{$escaped}%")
                        ->orWhere("excerpt->{$locale}", 'like', "%{$escaped}%")
                        ->orWhere("content->{$locale}", 'like', "%{$escaped}%")
                        ->orWhereJsonContains('tags', $escaped);
                });
            })
            ->when($activeTag !== '', fn ($query) => $query->whereJsonContains('tags', $activeTag));

        $blogPosts = (clone $blogPostsQuery)
            ->orderByDesc('published_at')
            ->paginate(config('app.per_page'))
            ->withQueryString();

        return view('blog.index', [
            'blogIndexUrl' => route('blog.index'),
            'blogPosts' => $blogPosts,
            'search' => $search,
            'activeTag' => $activeTag,
            'tagCounts' => $this->tagCounts(),
        ]);
    }

    public function show(BlogPost $blogPost): View
    {
        abort_unless($blogPost->isPublished(), 404);

        // Limit candidates upfront to avoid loading the entire posts table.
        $candidateRelatedPosts = BlogPost::query()
            ->with('author')
            ->published()
            ->whereKeyNot($blogPost->getKey())
            ->orderByDesc('published_at')
            ->limit(self::RELATED_CANDIDATES_LIMIT)
            ->get();

        $currentTags = $blogPost->normalizedTags();
        $currentKeywords = $this->keywords($blogPost->titleForLocale());

        $relatedPosts = $candidateRelatedPosts
            ->map(function (BlogPost $candidate) use ($currentTags, $currentKeywords): array {
                $sharedTagCount = $candidate->normalizedTags()
                    ->intersect($currentTags)
                    ->count();
                $titleOverlap = count(array_intersect(
                    $this->keywords($candidate->titleForLocale()),
                    $currentKeywords,
                ));

                return [
                    'post' => $candidate,
                    'score' => ($sharedTagCount * 10) + $titleOverlap,
                ];
            })
            ->filter(fn (array $entry): bool => $entry['score'] > 0)
            ->sortByDesc(fn (array $entry): int => $entry['score'])
            ->take(self::RELATED_POSTS_LIMIT)
            ->pluck('post');

        if ($relatedPosts->count() < self::RELATED_POSTS_LIMIT) {
            $fallbackPosts = $candidateRelatedPosts
                ->reject(fn (BlogPost $candidate): bool => $relatedPosts->contains(fn (BlogPost $relatedPost): bool => $relatedPost->is($candidate)))
                ->take(self::RELATED_POSTS_LIMIT - $relatedPosts->count());

            $relatedPosts = $relatedPosts->concat($fallbackPosts);
        }

        return view('blog.show', [
            'blogPost' => $blogPost->load('author'),
            'blogIndexUrl' => route('blog.index'),
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * @return Collection<string, int>
     */
    protected function tagCounts(): Collection
    {
        return Cache::remember('blog:tag_counts', now()->addMinutes(5), function (): Collection {
            return BlogPost::query()
                ->published()
                ->get(['tags'])
                ->flatMap(fn (BlogPost $blogPost): array => $blogPost->tagsList())
                ->countBy()
                ->sortDesc();
        });
    }

    /**
     * @return array<int, string>
     */
    protected function keywords(string $value): array
    {
        return collect(preg_split('/[^[:alnum:]]+/u', Str::lower($value)) ?: [])
            ->filter(fn (string $keyword): bool => $keyword !== '' && mb_strlen($keyword) >= 4)
            ->values()
            ->all();
    }
}
