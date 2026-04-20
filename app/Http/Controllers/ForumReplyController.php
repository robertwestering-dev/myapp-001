<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreForumReplyRequest;
use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Notifications\ForumReplyPosted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ForumReplyController extends Controller
{
    public function store(StoreForumReplyRequest $request, ForumThread $forumThread): RedirectResponse
    {
        Gate::authorize('view', $forumThread);
        Gate::authorize('create', ForumReply::class);

        abort_if($forumThread->is_locked, 403);

        $forumReply = $forumThread->replies()->create([
            'user_id' => $request->user()->getKey(),
            'body' => $request->validated('body'),
        ]);

        $forumThread->forceFill([
            'last_activity_at' => now(),
        ])->save();

        $forumThread->loadMissing('author');
        $threadAuthor = $forumThread->author;
        if ($threadAuthor && $threadAuthor->getKey() !== $request->user()->getKey()) {
            $forumReply->load('author');
            $threadAuthor->notify(new ForumReplyPosted($forumThread, $forumReply));
        }

        return redirect()
            ->to(route('forum.show', $forumThread).'#reply-form')
            ->with('status', __('hermes.forum.status.reply_created'));
    }

    public function update(
        StoreForumReplyRequest $request,
        ForumThread $forumThread,
        ForumReply $forumReply
    ): RedirectResponse {
        Gate::authorize('view', $forumThread);
        Gate::authorize('update', $forumReply);

        abort_if($forumThread->is_locked, 403);
        abort_unless($forumReply->forum_thread_id === $forumThread->getKey(), 404);

        $forumReply->forceFill([
            'body' => $request->validated('body'),
        ])->save();

        $forumThread->forceFill([
            'last_activity_at' => now(),
        ])->save();

        return redirect()
            ->to(route('forum.show', $forumThread).'#reply-'.$forumReply->getKey())
            ->with('status', __('hermes.forum.status.reply_updated'));
    }

    public function destroy(ForumThread $forumThread, ForumReply $forumReply): RedirectResponse
    {
        Gate::authorize('view', $forumThread);
        Gate::authorize('delete', $forumReply);

        abort_if($forumThread->is_locked, 403);
        abort_unless($forumReply->forum_thread_id === $forumThread->getKey(), 404);

        $forumReply->delete();

        $forumThread->forceFill([
            'last_activity_at' => $forumThread->replies()->latest('created_at')->value('created_at') ?? $forumThread->created_at,
        ])->save();

        return redirect()
            ->to(route('forum.show', $forumThread).'#replies')
            ->with('status', __('hermes.forum.status.reply_deleted'));
    }
}
