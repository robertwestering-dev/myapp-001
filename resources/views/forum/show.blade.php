<x-layouts.hermes-dashboard :title="$forumThread->title">
    <x-slot:head>
        <style>
            .forum-thread-page,
            .forum-thread-page__body,
            .forum-thread-page__stack,
            .forum-thread-page__reply-list,
            .forum-thread-page__reply,
            .forum-thread-page__sidebar,
            .forum-thread-form {
                display: grid;
                gap: 20px;
            }

            .forum-thread-page {
                gap: 24px;
            }

            .forum-thread-page__body {
                grid-template-columns: minmax(0, 1.5fr) minmax(280px, 0.85fr);
                align-items: start;
            }

            .forum-thread-page__prose,
            .forum-thread-page__reply-prose {
                font-family: Arial, Helvetica, sans-serif;
                color: #24302b;
                line-height: 1.8;
            }

            .forum-thread-page__prose h1,
            .forum-thread-page__prose h2,
            .forum-thread-page__prose h3,
            .forum-thread-page__reply-prose h1,
            .forum-thread-page__reply-prose h2,
            .forum-thread-page__reply-prose h3 {
                margin: 1.6em 0 0.55em;
                line-height: 1.18;
            }

            .forum-thread-page__prose h1,
            .forum-thread-page__reply-prose h1 {
                font-size: 1.45rem;
            }

            .forum-thread-page__prose h2,
            .forum-thread-page__reply-prose h2 {
                font-size: 1.25rem;
            }

            .forum-thread-page__prose h3,
            .forum-thread-page__reply-prose h3 {
                font-size: 1.08rem;
            }

            .forum-thread-page__prose p,
            .forum-thread-page__prose ul,
            .forum-thread-page__prose ol,
            .forum-thread-page__prose blockquote,
            .forum-thread-page__reply-prose p,
            .forum-thread-page__reply-prose ul,
            .forum-thread-page__reply-prose ol,
            .forum-thread-page__reply-prose blockquote {
                margin: 0 0 1rem;
            }

            .forum-thread-page__prose ul,
            .forum-thread-page__prose ol,
            .forum-thread-page__reply-prose ul,
            .forum-thread-page__reply-prose ol {
                padding-left: 1.35rem;
            }

            .forum-thread-page__prose li,
            .forum-thread-page__reply-prose li {
                margin-bottom: 0.45rem;
            }

            .forum-thread-page__prose blockquote,
            .forum-thread-page__reply-prose blockquote {
                padding: 0.85rem 1rem;
                border-left: 4px solid rgba(32, 69, 58, 0.24);
                background: rgba(32, 69, 58, 0.06);
                border-radius: 0 14px 14px 0;
            }

            .forum-thread-page__prose a,
            .forum-thread-page__reply-prose a {
                color: #a84a19;
                text-decoration: underline;
                text-underline-offset: 0.12em;
            }

            .user-surface-card--accent .forum-thread-page__prose {
                color: rgba(246, 239, 229, 0.92);
            }

            .user-surface-card--accent .forum-thread-page__prose h1,
            .user-surface-card--accent .forum-thread-page__prose h2,
            .user-surface-card--accent .forum-thread-page__prose h3,
            .user-surface-card--accent .forum-thread-page__prose strong {
                color: #f6efe5;
            }

            .user-surface-card--accent .forum-thread-page__prose blockquote {
                border-left-color: rgba(246, 239, 229, 0.34);
                background: rgba(255, 255, 255, 0.08);
            }

            .user-surface-card--accent .forum-thread-page__prose a {
                color: #f6efe5;
            }

            .forum-thread-page__prose > :first-child,
            .forum-thread-page__reply-prose > :first-child {
                margin-top: 0;
            }

            .forum-thread-page__prose > :last-child,
            .forum-thread-page__reply-prose > :last-child {
                margin-bottom: 0;
            }

            .forum-thread-page__reply-header {
                display: flex;
                gap: 16px;
                justify-content: space-between;
                align-items: end;
                flex-wrap: wrap;
            }

            .forum-thread-page__sidebar {
                align-self: start;
                position: sticky;
                top: 108px;
            }

            .forum-textarea {
                width: 100%;
                min-height: 180px;
                padding: 14px 16px;
                border-radius: 18px;
                border: 1px solid rgba(22, 33, 29, 0.12);
                background: rgba(255, 255, 255, 0.92);
                color: #16211d;
                font: inherit;
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.6;
                resize: vertical;
            }

            .forum-thread-form__label {
                display: grid;
                gap: 8px;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 700;
                color: #20453a;
            }

            .forum-thread-form__helper,
            .forum-thread-page__empty {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                line-height: 1.6;
            }

            .forum-errors {
                margin: 0;
                padding-left: 18px;
                display: grid;
                gap: 8px;
                font-family: Arial, Helvetica, sans-serif;
                color: #a84a19;
            }

            .forum-reply-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                align-items: center;
            }

            .forum-reply-link {
                border: 0;
                padding: 0;
                background: transparent;
                color: #a84a19;
                font: inherit;
                font-family: Arial, Helvetica, sans-serif;
                cursor: pointer;
                text-decoration: underline;
                text-underline-offset: 0.12em;
            }

            @media (max-width: 980px) {
                .forum-thread-page__body {
                    grid-template-columns: 1fr;
                }

                .forum-thread-page__sidebar {
                    position: static;
                    top: auto;
                }
            }
        </style>
    </x-slot:head>

    <div class="forum-thread-page">
        <x-user-feedback variant="status" :messages="session('status')" />

        <div class="forum-thread-page__body">
            <div class="forum-thread-page__stack">
                <section class="user-surface-card user-surface-card--accent">
                    <x-user-page-heading
                        :eyebrow="__('hermes.forum.detail.eyebrow')"
                        :title="$forumThread->title"
                    />

                    <div class="forum-thread-page__prose">{!! $forumThread->renderedBody() !!}</div>

                    <x-user-inline-meta
                        class="user-inline-meta--light"
                        :items="[
                            __('hermes.forum.meta.started_by', ['name' => $forumThread->author->first_name ?: $forumThread->author->name]),
                            __('hermes.forum.meta.created', ['datetime' => $forumThread->created_at->format('d-m-Y H:i')]),
                            __('hermes.forum.meta.replies', ['count' => $forumThread->replies->count()]),
                        ]"
                    />
                </section>

                <section class="user-panel user-panel--padded" id="replies">
                    <x-user-section-heading
                        :eyebrow="__('hermes.forum.replies.eyebrow')"
                        :title="trans_choice('hermes.forum.replies.title', $forumThread->replies->count(), ['count' => $forumThread->replies->count()])"
                        :text="__('hermes.forum.replies.text')"
                    />

                    <div class="forum-thread-page__reply-list">
                        @forelse ($forumThread->replies as $forumReply)
                            <x-user-surface-card variant="soft" class="forum-thread-page__reply" id="reply-{{ $forumReply->getKey() }}">
                                <div class="forum-thread-page__reply-header">
                                    <strong>{{ $forumReply->author->first_name ?: $forumReply->author->name }}</strong>
                                    <x-user-inline-meta
                                        :items="[
                                            __('hermes.forum.meta.created', ['datetime' => $forumReply->created_at->format('d-m-Y H:i')]),
                                        ]"
                                    />
                                </div>

                                @if ((int) $editingReplyId === $forumReply->getKey() && auth()->user()->can('update', $forumReply))
                                    <form method="POST" action="{{ route('forum-replies.update', [$forumThread, $forumReply]) }}" class="forum-thread-form">
                                        @csrf
                                        @method('PUT')

                                        <label class="forum-thread-form__label">
                                            <span>{{ __('hermes.forum.reply_form.edit_body') }}</span>
                                            <textarea name="body" class="forum-textarea" required>{{ old('body', $forumReply->body) }}</textarea>
                                        </label>

                                        <x-user-action-row>
                                            <button type="submit" class="pill">{{ __('hermes.forum.reply_form.update') }}</button>
                                            <a href="{{ route('forum.show', $forumThread).'#reply-'.$forumReply->getKey() }}" class="pill pill--neutral">{{ __('hermes.forum.reply_form.cancel') }}</a>
                                        </x-user-action-row>
                                    </form>
                                @else
                                    <div class="forum-thread-page__reply-prose">{!! $forumReply->renderedBody() !!}</div>
                                @endif

                                @if (auth()->user()->can('update', $forumReply) || auth()->user()->can('delete', $forumReply))
                                    <div class="forum-reply-actions">
                                        @can('update', $forumReply)
                                            <a href="{{ route('forum.show', ['forumThread' => $forumThread, 'edit_reply' => $forumReply->getKey()]) }}#reply-{{ $forumReply->getKey() }}" class="forum-reply-link">
                                                {{ __('hermes.forum.reply_form.edit') }}
                                            </a>
                                        @endcan

                                        @can('delete', $forumReply)
                                            <form method="POST" action="{{ route('forum-replies.destroy', [$forumThread, $forumReply]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="forum-reply-link">{{ __('hermes.forum.reply_form.delete') }}</button>
                                            </form>
                                        @endcan
                                    </div>
                                @endif
                            </x-user-surface-card>
                        @empty
                            <p class="forum-thread-page__empty">{{ __('hermes.forum.replies.empty') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="forum-thread-page__sidebar">
                <section class="user-panel user-panel--padded" id="reply-form">
                    <x-user-section-heading
                        :eyebrow="__('hermes.forum.reply_form.eyebrow')"
                        :title="__('hermes.forum.reply_form.title')"
                        :text="__('hermes.forum.reply_form.text')"
                    />

                    @if ($forumThread->is_locked)
                        <x-user-feedback variant="subtle" :messages="__('hermes.forum.reply_form.locked')" />
                    @else
                        <form method="POST" action="{{ route('forum-replies.store', $forumThread) }}" class="forum-thread-form">
                            @csrf

                            <label class="forum-thread-form__label">
                                <span>{{ __('hermes.forum.reply_form.body') }}</span>
                                <textarea name="body" class="forum-textarea" required>{{ old('body') }}</textarea>
                            </label>

                            <p class="forum-thread-form__helper">{{ __('hermes.forum.reply_form.helper') }}</p>

                            @if ($errors->isNotEmpty())
                                <ul class="forum-errors">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <x-user-action-row>
                                <button type="submit" class="pill">{{ __('hermes.forum.reply_form.submit') }}</button>
                                <a href="{{ route('forum.index') }}" class="pill pill--neutral">{{ __('hermes.forum.back_to_overview') }}</a>
                            </x-user-action-row>
                        </form>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-layouts.hermes-dashboard>
