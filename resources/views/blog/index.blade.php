<x-layouts.hermes-public
    :title="$activeTag !== '' ? __('hermes.blog.title_topic', ['topic' => $activeTag]) : __('hermes.blog.title')"
    :meta-description="$activeTag !== '' ? __('hermes.blog.meta_topic', ['topic' => $activeTag]) : __('hermes.blog.meta_description')"
    :canonical-url="$activeTag !== '' ? route('blog.index', ['tag' => $activeTag]) : $blogIndexUrl"
    :meta-image="$blogPosts->first()?->cover_image_url"
    :show-header-booking="false"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'Blog',
        'name' => __('hermes.blog.title'),
        'description' => $activeTag !== '' ? __('hermes.blog.meta_topic', ['topic' => $activeTag]) : __('hermes.blog.meta_description'),
        'url' => $activeTag !== '' ? route('blog.index', ['tag' => $activeTag]) : $blogIndexUrl,
    ]"
>
    @guest
        <x-slot:headerMenu>
            <a class="home-menu-item" href="{{ route('home') }}">Home</a>
            <a class="home-menu-item" href="{{ route('blog.index') }}">Blog</a>
            <details class="home-menu-dropdown">
                <summary class="home-menu-trigger">
                    Over
                    <span aria-hidden="true">▾</span>
                </summary>
                <div class="home-submenu">
                    <a href="{{ route('inspiration-sources.show') }}">Inspiratiebronnen</a>
                    <a href="{{ route('about.show') }}">Over ons</a>
                    <a href="{{ route('pricing.show') }}">Prijzen</a>
                    <a href="{{ route('privacy.show') }}">{{ __('hermes.footer.privacy') }}</a>
                </div>
            </details>
            <a class="home-menu-item" href="{{ route('organizations.landing') }}">Organisaties</a>
            <a class="home-menu-item" href="{{ route('contact.show') }}">Contact</a>
        </x-slot:headerMenu>
    @endguest

    <x-slot:head>
        <style>
            .blog-page {
                display: grid;
                gap: 24px;
            }

            .blog-content {
                display: grid;
                gap: 24px;
                grid-template-columns: minmax(0, 1.45fr) minmax(280px, 0.78fr);
                align-items: start;
            }

            .blog-list,
            .blog-sidebar,
            .blog-article-card,
            .blog-article-card__header,
            .blog-article-card__meta,
            .blog-article-card__tags,
            .blog-sidebar__tags,
            .blog-search-form {
                display: grid;
                gap: 16px;
            }

            .blog-search-form__actions,
            .blog-article-card__actions {
                display: flex;
                flex-wrap: wrap;
                gap: 12px;
                align-items: center;
            }

            .blog-search-label {
                display: grid;
                gap: 8px;
                font-family: Arial, Helvetica, sans-serif;
                font-weight: 700;
                color: #20453a;
            }

            .blog-search-field {
                width: 100%;
                min-height: 52px;
                padding: 0 16px;
                border-radius: 18px;
                border: 1px solid rgba(22, 33, 29, 0.12);
                background: rgba(255, 255, 255, 0.92);
                color: #16211d;
                font: inherit;
                font-family: Arial, Helvetica, sans-serif;
            }

            .blog-article-card__header {
                grid-template-columns: minmax(0, 1fr) auto;
                align-items: start;
            }

            .blog-article-card__title {
                margin: 0;
                font-size: 1.2rem;
                line-height: 1.15;
            }

            .blog-article-card__summary,
            .blog-sidebar__note,
            .blog-empty-text {
                margin: 0;
                font-family: Arial, Helvetica, sans-serif;
                color: #5a6762;
                line-height: 1.6;
            }

            .blog-article-card__tags,
            .blog-sidebar__tags {
                grid-template-columns: repeat(auto-fit, minmax(120px, max-content));
            }

            .blog-chip,
            .blog-badge {
                display: inline-flex;
                align-items: center;
                width: fit-content;
                padding: 8px 12px;
                border-radius: 999px;
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.85rem;
                font-weight: 700;
            }

            .blog-chip {
                background: rgba(32, 69, 58, 0.08);
                border: 1px solid rgba(32, 69, 58, 0.12);
                color: #20453a;
            }

            .blog-chip--active {
                background: rgba(217, 106, 43, 0.12);
                border-color: rgba(217, 106, 43, 0.18);
                color: #a84a19;
            }

            .blog-badge {
                background: rgba(42, 106, 109, 0.12);
                color: #20575b;
            }

            .blog-pagination {
                display: flex;
                justify-content: center;
            }

            @media (max-width: 980px) {
                .blog-content,
                .blog-article-card__header {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <div class="blog-page">
        <div class="blog-content">
            <section class="blog-list">
                @forelse ($blogPosts as $blogPost)
                    <x-user-surface-card variant="soft" class="blog-article-card">
                        <div class="blog-article-card__header">
                            <div class="blog-article-card__meta">
                                <x-user-inline-meta
                                    :items="[
                                        $blogPost->author?->name ?? __('hermes.blog.editorial_team'),
                                        $blogPost->published_at?->translatedFormat('d-m-Y'),
                                        __('hermes.blog.reading_time', ['minutes' => $blogPost->readingTimeInMinutes()]),
                                    ]"
                                />

                                <h2 class="blog-article-card__title">
                                    <a href="{{ route('blog.show', $blogPost) }}">{{ $blogPost->titleForLocale() }}</a>
                                </h2>
                            </div>

                            <span class="blog-badge">{{ __('hermes.blog.article_badge') }}</span>
                        </div>

                        <p class="blog-article-card__summary">{{ $blogPost->excerptForLocale() }}</p>

                        @if ($blogPost->tagsList() !== [])
                            <div class="blog-article-card__tags">
                                @foreach ($blogPost->tagsList() as $tag)
                                    <a
                                        href="{{ route('blog.index', array_merge(request()->except('page'), ['tag' => $tag])) }}"
                                        @class(['blog-chip', 'blog-chip--active' => $activeTag === $tag])
                                    >
                                        {{ $tag }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <div class="blog-article-card__actions">
                            <a href="{{ route('blog.show', $blogPost) }}" class="pill">{{ __('hermes.blog.read_post') }}</a>
                        </div>
                    </x-user-surface-card>
                @empty
                    <x-user-guidance-card
                        :eyebrow="__('hermes.blog.eyebrow')"
                        :title="__('hermes.blog.empty_title')"
                        :text="__('hermes.blog.empty_text')"
                    />
                @endforelse

                @if ($blogPosts->count() > 0)
                    <div class="blog-pagination">
                        {{ $blogPosts->links() }}
                    </div>
                @endif
            </section>

            <aside class="blog-sidebar">
                <section class="user-filter-panel" aria-labelledby="blog-overview-title">
                    <x-user-section-heading
                        id="blog-overview-title"
                        :eyebrow="__('hermes.blog.summary_eyebrow')"
                        :title="__('hermes.blog.summary_title')"
                        :text="$activeTag !== '' ? __('hermes.blog.active_topic', ['topic' => $activeTag]) : __('hermes.blog.summary_text')"
                    />

                    <p class="blog-sidebar__note">{{ __('hermes.blog.editorial_note') }}</p>

                    <form method="GET" action="{{ route('blog.index') }}" class="blog-search-form">
                        <label class="blog-search-label">
                            <span>{{ __('hermes.blog.search_label') }}</span>
                            <input
                                type="search"
                                name="search"
                                value="{{ $search }}"
                                class="blog-search-field"
                                placeholder="{{ __('hermes.blog.search_placeholder') }}"
                            >
                        </label>

                        @if ($activeTag !== '')
                            <input type="hidden" name="tag" value="{{ $activeTag }}">
                        @endif

                        <div class="blog-search-form__actions">
                            <button type="submit" class="pill">{{ __('hermes.blog.search_action') }}</button>
                            <a href="{{ route('blog.index') }}" class="pill pill--neutral">{{ __('hermes.blog.reset_action') }}</a>
                        </div>
                    </form>

                    @if ($tagCounts->isNotEmpty())
                        <div class="blog-sidebar__tags">
                            <a
                                href="{{ route('blog.index', request()->except(['tag', 'page'])) }}"
                                @class(['blog-chip', 'blog-chip--active' => $activeTag === ''])
                            >
                                {{ __('hermes.blog.all_topics') }}
                            </a>

                            @foreach ($tagCounts->take(8) as $tag => $count)
                                <a
                                    href="{{ route('blog.index', array_merge(request()->except('page'), ['tag' => $tag])) }}"
                                    @class(['blog-chip', 'blog-chip--active' => $activeTag === $tag])
                                >
                                    {{ $tag }} ({{ $count }})
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="blog-empty-text">{{ __('hermes.blog.empty_topics') }}</p>
                    @endif
                </section>
            </aside>
        </div>
    </div>
</x-layouts.hermes-public>
