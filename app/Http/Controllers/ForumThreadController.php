<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForumThreadRequest;
use App\Models\ForumReply;
use App\Models\ForumThread;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class ForumThreadController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ForumThread::class);

        $search = trim((string) $request->string('search'));
        $activeType = trim((string) $request->string('type'));
        $activeTag = trim((string) $request->string('tag'));

        $forumThreadsQuery = ForumThread::query()
            ->with('author')
            ->withCount('replies')
            ->when($search !== '', function ($query) use ($search): void {
                $escaped = str_replace(['%', '_'], ['\\%', '\\_'], $search);

                $query->where(function ($forumThreadQuery) use ($escaped): void {
                    $forumThreadQuery
                        ->where('title', 'like', "%{$escaped}%")
                        ->orWhere('body', 'like', "%{$escaped}%");
                });
            })
            ->when($activeType !== '', fn ($query) => $query->where('discussion_type', $activeType))
            ->when($activeTag !== '', fn ($query) => $query->whereJsonContains('tags', $activeTag));

        return view('forum.index', [
            'forumThreads' => (clone $forumThreadsQuery)
                ->recent()
                ->paginate(config('app.per_page'))
                ->withQueryString(),
            'search' => $search,
            'activeType' => $activeType,
            'activeTag' => $activeTag,
            'forumThreadCount' => ForumThread::query()->count(),
            'forumReplyCount' => ForumReply::query()->count(),
            'questionThreadCount' => ForumThread::query()
                ->where('discussion_type', ForumThread::TYPE_QUESTION)
                ->count(),
            'tagCounts' => $this->tagCounts(),
        ]);
    }

    public function show(Request $request, ForumThread $forumThread): View
    {
        Gate::authorize('view', $forumThread);

        $editingReplyId = $request->integer('edit_reply');

        return view('forum.show', [
            'forumThread' => $forumThread->load([
                'author',
                'replies.author',
            ]),
            'editingReplyId' => $editingReplyId,
        ]);
    }

    public function store(StoreForumThreadRequest $request): RedirectResponse
    {
        Gate::authorize('create', ForumThread::class);

        $forumThread = ForumThread::createWithUniqueSlug(
            $request->validated(),
            ['user_id' => $request->user()->getKey(), 'last_activity_at' => now()],
        );

        return redirect()
            ->route('forum.show', $forumThread)
            ->with('status', __('hermes.forum.status.thread_created'));
    }

    /**
     * @return Collection<string, int>
     */
    protected function tagCounts(): Collection
    {
        /** @var array<string, int> $cached */
        $cached = Cache::remember('forum:tag_counts', now()->addMinutes(5), function (): array {
            return ForumThread::query()
                ->get(['tags'])
                ->flatMap(fn (ForumThread $forumThread): array => $forumThread->tagList()->all())
                ->countBy()
                ->sortDesc()
                ->take(8)
                ->all();
        });

        return collect($cached);
    }
}
