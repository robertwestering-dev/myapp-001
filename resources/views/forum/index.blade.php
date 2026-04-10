<x-layouts.hermes-dashboard :title="__('hermes.forum.title')">
    <x-slot:head>
        <style>
            .forum-page {
                display: grid;
                gap: 24px;
            }

            .forum-content {
                display: grid;
                gap: 24px;
            }

            .forum-list,
            .forum-sidebar,
            .forum-filters__fields,
            .forum-thread-card,
            .forum-thread-card__header,
            .forum-thread-card__meta,
            .forum-thread-card__tags,
            .forum-tag-cloud,
            .forum-form,
            .forum-form__grid {
                display: grid;
                gap: 16px;
            }

            .forum-content {
                grid-template-columns: minmax(0, 1.45fr) minmax(300px, 0.9fr);
                align-items: start;
            }

            .forum-field,
            .forum-select,
            .forum-textarea {
                width: 100%;
                border-radius: 18px;
                border: 1px solid rgba(22, 33, 29, 0.12);
                background: rgba(255, 255, 255, 0.92);
                color: #16211d;
                font: inherit;
                font-family: Arial, Helvetica, sans-serif;
            }

            .forum-field,
            .forum-select {
                min-height: 52px;
                padding: 0 16px;
            }

            .forum-textarea {
                min-height: 180px;
                padding: 14px 16px;
                resize: vertical;
                line-height: 1.6;
            }

            .forum-filters__fields,
            .forum-form__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .forum-filters__actions,
            .forum-thread-card__actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
            }

            .forum-thread-card__header {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: start;
            }

            .forum-thread-card__summary,
            .forum-form label,
            .forum-empty-text {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                line-height: 1.6;
            }

            .forum-thread-card__title {
                margin: 0;
                font-size: 1.2rem;
                line-height: 1.15;
            }

            .forum-thread-card__tags,
            .forum-tag-cloud {
                grid-template-columns: repeat(auto-fit, minmax(120px, max-content));
            }

            .forum-chip,
            .forum-badge {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                padding: 8px 12px;
                border-radius: 999px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.85rem;
                font-weight: 700;
            }

            .forum-chip {
                background: rgba(32, 69, 58, 0.08);
                border: 1px solid rgba(32, 69, 58, 0.12);
                color: #20453a;
            }

            .forum-badge {
                background: rgba(217, 106, 43, 0.12);
                color: #a84a19;
            }

            .forum-badge--question {
                background: rgba(42, 106, 109, 0.12);
                color: #20575b;
            }

            .forum-badge--experience {
                background: rgba(32, 69, 58, 0.1);
                color: #20453a;
            }

            .forum-badge--insight {
                background: rgba(217, 106, 43, 0.12);
                color: #a84a19;
            }

            .forum-form__helper {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                font-size: 0.94rem;
                line-height: 1.6;
            }

            .forum-form__label {
                display: grid;
                gap: 8px;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 700;
                color: #20453a;
            }

            .forum-errors {
                margin: 0;
                padding-left: 18px;
                display: grid;
                gap: 8px;
                font-family: Arial, Helvetica, sans-serif;
                color: #a84a19;
            }

            @media (max-width: 980px) {
                .forum-content,
                .forum-filters__fields,
                .forum-form__grid,
                .forum-thread-card__header {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <div class="forum-page">
        <div class="forum-content">
            <section class="forum-list">
                <x-user-feedback variant="status" :messages="session('status')" />

                @if ($errors->hasBag('default') && old('_source') === 'thread')
                    <x-user-feedback variant="errors" :messages="__('hermes.forum.validation.fix_thread_errors')" />
                @endif

                @forelse ($forumThreads as $forumThread)
                    <x-user-surface-card variant="soft" class="forum-thread-card">
                        <div class="forum-thread-card__header">
                            <div class="forum-thread-card__meta">
                                <x-user-inline-meta
                                    :items="[
                                        __('hermes.forum.meta.started_by', ['name' => $forumThread->author->first_name ?: $forumThread->author->name]),
                                        __('hermes.forum.meta.updated', ['datetime' => $forumThread->last_activity_at?->format('d-m-Y H:i')]),
                                        __('hermes.forum.meta.replies', ['count' => $forumThread->replies_count]),
                                    ]"
                                />
                                <h2 class="forum-thread-card__title">
                                    <a href="{{ route('forum.show', $forumThread) }}">{{ $forumThread->title }}</a>
                                </h2>
                            </div>

                            <span class="forum-badge forum-badge--{{ $forumThread->discussion_type }}">
                                {{ __('hermes.forum.types.'.$forumThread->discussion_type) }}
                            </span>
                        </div>

                        <p class="forum-thread-card__summary">{{ $forumThread->excerpt() }}</p>

                        @if ($forumThread->tagList()->isNotEmpty())
                            <div class="forum-thread-card__tags">
                                @foreach ($forumThread->tagList() as $tag)
                                    <a href="{{ route('forum.index', ['tag' => $tag]) }}" class="forum-chip">{{ $tag }}</a>
                                @endforeach
                            </div>
                        @endif

                        <div class="forum-thread-card__actions">
                            <a href="{{ route('forum.show', $forumThread) }}" class="pill">{{ __('hermes.forum.open_thread') }}</a>
                        </div>
                    </x-user-surface-card>
                @empty
                    <x-user-guidance-card
                        :eyebrow="__('hermes.forum.empty.eyebrow')"
                        :title="__('hermes.forum.empty.title')"
                        :text="__('hermes.forum.empty.text')"
                        :action-label="__('hermes.forum.empty.action')"
                        :action-href="route('forum.index').'#new-thread-form'"
                    />
                @endforelse

                {{ $forumThreads->links() }}
            </section>

            <aside class="forum-sidebar">
                <section class="user-filter-panel forum-filters" aria-labelledby="forum-filters-title">
                    <x-user-section-heading
                        id="forum-filters-title"
                        :eyebrow="__('hermes.forum.filters.eyebrow')"
                        :title="__('hermes.forum.filters.title')"
                        :text="__('hermes.forum.filters.text')"
                    />

                    <form method="GET" action="{{ route('forum.index') }}" class="forum-form">
                        <div class="forum-filters__fields">
                            <label class="forum-form__label">
                                <span>{{ __('hermes.forum.filters.search') }}</span>
                                <input type="text" name="search" value="{{ $search }}" class="forum-field" placeholder="{{ __('hermes.forum.filters.search_placeholder') }}">
                            </label>

                            <label class="forum-form__label">
                                <span>{{ __('hermes.forum.filters.type') }}</span>
                                <select name="type" class="forum-select">
                                    <option value="">{{ __('hermes.forum.filters.all_types') }}</option>
                                    @foreach (\App\Models\ForumThread::discussionTypeOptions() as $discussionType)
                                        <option value="{{ $discussionType }}" @selected($activeType === $discussionType)>{{ __('hermes.forum.types.'.$discussionType) }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div class="forum-filters__actions">
                            <button type="submit" class="pill">{{ __('hermes.forum.filters.apply') }}</button>
                            <a href="{{ route('forum.index') }}" class="pill pill--neutral">{{ __('hermes.forum.filters.reset') }}</a>
                        </div>
                    </form>
                </section>

                <section class="user-panel user-panel--padded" id="new-thread-form">
                    <x-user-section-heading
                        :eyebrow="__('hermes.forum.compose.eyebrow')"
                        :title="__('hermes.forum.compose.title')"
                        :text="__('hermes.forum.compose.text')"
                    />

                    <form method="POST" action="{{ route('forum.store') }}" class="forum-form">
                        @csrf
                        <input type="hidden" name="_source" value="thread">

                        <div class="forum-form__grid">
                            <label class="forum-form__label">
                                <span>{{ __('hermes.forum.fields.discussion_type') }}</span>
                                <select name="discussion_type" class="forum-select" required>
                                    @foreach (\App\Models\ForumThread::discussionTypeOptions() as $discussionType)
                                        <option value="{{ $discussionType }}" @selected(old('discussion_type', \App\Models\ForumThread::TYPE_QUESTION) === $discussionType)>{{ __('hermes.forum.types.'.$discussionType) }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="forum-form__label">
                                <span>{{ __('hermes.forum.fields.tags') }}</span>
                                <input type="text" name="tags" value="{{ old('tags') }}" class="forum-field" placeholder="{{ __('hermes.forum.fields.tags_placeholder') }}">
                            </label>
                        </div>

                        <label class="forum-form__label">
                            <span>{{ __('hermes.forum.fields.title') }}</span>
                            <input type="text" name="title" value="{{ old('title') }}" class="forum-field" maxlength="160" required>
                        </label>

                        <label class="forum-form__label">
                            <span>{{ __('hermes.forum.fields.body') }}</span>
                            <textarea name="body" class="forum-textarea" required>{{ old('body') }}</textarea>
                        </label>

                        <p class="forum-form__helper">{{ __('hermes.forum.compose.helper') }}</p>

                        @if ($errors->hasBag('default') && old('_source') === 'thread')
                            <ul class="forum-errors">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <x-user-action-row>
                            <button type="submit" class="pill">{{ __('hermes.forum.compose.submit') }}</button>
                        </x-user-action-row>
                    </form>
                </section>

                <section class="user-panel user-panel--padded">
                    <x-user-section-heading
                        :eyebrow="__('hermes.forum.tags.eyebrow')"
                        :title="__('hermes.forum.tags.title')"
                        :text="$activeTag !== '' ? __('hermes.forum.tags.active', ['tag' => $activeTag]) : __('hermes.forum.tags.text')"
                    />

                    @if ($tagCounts->isNotEmpty())
                        <div class="forum-tag-cloud">
                            @foreach ($tagCounts as $tag => $count)
                                <a href="{{ route('forum.index', ['tag' => $tag]) }}" class="forum-chip">{{ $tag }} ({{ $count }})</a>
                            @endforeach
                        </div>
                    @else
                        <p class="forum-empty-text">{{ __('hermes.forum.tags.empty') }}</p>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-layouts.hermes-dashboard>
