<x-layouts.hermes-public :title="__('hermes.blog.title')">
    <x-slot:head>
        <style>
            .blog-hero,
            .blog-toolbar,
            .blog-grid,
            .tag-cloud,
            .blog-stats,
            .card-actions,
            .empty-state {
                display: grid;
                gap: 18px;
            }

            .blog-hero {
                grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
                align-items: stretch;
            }

            .blog-hero__panel,
            .blog-hero__featured,
            .blog-toolbar,
            .blog-card,
            .blog-stats,
            .empty-state {
                border: 1px solid rgba(255, 255, 255, 0.58);
                box-shadow: var(--shadow);
                border-radius: var(--radius-xl);
            }

            .blog-hero__panel,
            .blog-toolbar,
            .blog-card,
            .empty-state {
                background: var(--panel);
            }

            .blog-hero__panel,
            .blog-hero__featured,
            .blog-toolbar,
            .blog-card,
            .empty-state {
                padding: 28px;
            }

            .blog-hero__panel {
                background:
                    radial-gradient(circle at 82% 24%, rgba(214, 179, 122, 0.28), transparent 24%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(250, 243, 234, 0.82));
            }

            .blog-hero__lead,
            .blog-card__excerpt,
            .meta,
            .blog-toolbar label,
            .empty-state p {
                color: var(--muted);
                line-height: 1.75;
            }

            .blog-hero__lead {
                max-width: 62ch;
                font-size: 1.04rem;
            }

            .blog-stats {
                margin-top: 22px;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                background: rgba(255, 255, 255, 0.56);
                padding: 18px;
            }

            .blog-stats strong {
                display: block;
                font-size: 1.45rem;
                font-family: "Georgia", "Times New Roman", serif;
            }

            .blog-stats span {
                color: var(--muted);
                font-size: 0.92rem;
            }

            .blog-hero__featured {
                color: #f8f1e7;
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
            }

            .blog-hero__featured h2,
            .blog-hero__featured p,
            .blog-hero__featured a,
            .blog-hero__featured .meta {
                color: inherit;
            }

            .blog-toolbar {
                margin-top: 26px;
                grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
                align-items: end;
            }

            .blog-toolbar form {
                display: grid;
                gap: 12px;
            }

            .blog-toolbar input {
                width: 100%;
                padding: 14px 16px;
                border-radius: 16px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.85);
                font: inherit;
                color: var(--ink);
            }

            .tag-cloud {
                grid-template-columns: repeat(auto-fit, minmax(140px, max-content));
                align-content: start;
            }

            .tag-chip {
                display: inline-flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                padding: 10px 14px;
                border-radius: 999px;
                border: 1px solid var(--line);
                background: rgba(255, 255, 255, 0.74);
                font-size: 0.94rem;
            }

            .tag-chip--active {
                background: rgba(188, 91, 44, 0.12);
                border-color: rgba(188, 91, 44, 0.18);
                color: var(--clay-deep);
            }

            .blog-grid {
                margin-top: 26px;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .blog-card {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .blog-card__image,
            .blog-hero__featured-image {
                width: 100%;
                aspect-ratio: 16 / 9;
                object-fit: cover;
                border-radius: 20px;
            }

            .blog-card__placeholder,
            .blog-hero__placeholder {
                width: 100%;
                aspect-ratio: 16 / 9;
                border-radius: 20px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.36), transparent 24%),
                    linear-gradient(135deg, rgba(188, 91, 44, 0.3), rgba(30, 71, 61, 0.92));
            }

            .blog-card h3,
            .blog-hero__featured h2 {
                font-size: 1.5rem;
                line-height: 1.1;
                margin-bottom: 10px;
            }

            .meta {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                font-size: 0.92rem;
            }

            .tag-list {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .tag-list span {
                display: inline-flex;
                align-items: center;
                padding: 7px 11px;
                border-radius: 999px;
                background: rgba(30, 71, 61, 0.08);
                color: var(--forest);
                font-size: 0.84rem;
                font-weight: 600;
            }

            .card-actions {
                grid-auto-flow: column;
                justify-content: start;
                margin-top: auto;
            }

            .pagination {
                margin-top: 26px;
                display: flex;
                justify-content: center;
            }

            @media (max-width: 1040px) {
                .blog-hero,
                .blog-toolbar,
                .blog-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <section class="blog-hero">
        <div class="blog-hero__panel">
            <span class="eyebrow">{{ __('hermes.blog.eyebrow') }}</span>
            <h1>{{ __('hermes.blog.heading') }}</h1>
            <p class="blog-hero__lead">{{ __('hermes.blog.intro') }}</p>

            <div class="blog-stats">
                <div>
                    <strong>{{ ($featuredPost ? 1 : 0) + $blogPosts->total() }}</strong>
                    <span>{{ __('hermes.blog.stats_posts') }}</span>
                </div>
                <div>
                    <strong>{{ $tagCounts->count() }}</strong>
                    <span>{{ __('hermes.blog.stats_topics') }}</span>
                </div>
                <div>
                    <strong>{{ $featuredPost?->author?->name ?? __('hermes.blog.stats_editorial') }}</strong>
                    <span>{{ __('hermes.blog.stats_latest') }}</span>
                </div>
            </div>
        </div>

        <aside class="blog-hero__featured">
            <span class="eyebrow eyebrow--light">{{ __('hermes.blog.featured') }}</span>

            @if ($featuredPost)
                @if ($featuredPost->cover_image_url)
                    <img class="blog-hero__featured-image" src="{{ $featuredPost->cover_image_url }}" alt="{{ $featuredPost->titleForLocale() }}">
                @else
                    <div class="blog-hero__placeholder" aria-hidden="true"></div>
                @endif

                <div class="meta">
                    <span>{{ $featuredPost->author?->name ?? __('hermes.blog.editorial_team') }}</span>
                    <span>{{ $featuredPost->published_at?->translatedFormat('j M Y') }}</span>
                    <span>{{ __('hermes.blog.reading_time', ['minutes' => $featuredPost->readingTimeInMinutes()]) }}</span>
                </div>

                <div>
                    <h2>{{ $featuredPost->titleForLocale() }}</h2>
                    <p>{{ $featuredPost->excerptForLocale() }}</p>
                </div>

                <a class="pill pill--strong" href="{{ route('blog.show', $featuredPost) }}">{{ __('hermes.blog.read_post') }}</a>
            @else
                <h2>{{ __('hermes.blog.empty_title') }}</h2>
                <p>{{ __('hermes.blog.empty_text') }}</p>
            @endif
        </aside>
    </section>

    <section class="blog-toolbar">
        <form method="GET" action="{{ route('blog.index') }}">
            <label for="search">{{ __('hermes.blog.search_label') }}</label>
            <input id="search" type="search" name="search" value="{{ $search }}" placeholder="{{ __('hermes.blog.search_placeholder') }}">
            @if ($activeTag !== '')
                <input type="hidden" name="tag" value="{{ $activeTag }}">
            @endif

            <div class="card-actions">
                <button type="submit" class="pill pill--strong">{{ __('hermes.blog.search_action') }}</button>
                <a class="pill" href="{{ route('blog.index') }}">{{ __('hermes.blog.reset_action') }}</a>
            </div>
        </form>

        <div>
            <p class="meta">{{ __('hermes.blog.tag_intro') }}</p>
            <div class="tag-cloud">
                <a href="{{ route('blog.index', request()->except(['tag', 'page'])) }}" @class(['tag-chip', 'tag-chip--active' => $activeTag === ''])>
                    <span>{{ __('hermes.blog.all_topics') }}</span>
                </a>

                @foreach ($tagCounts->take(8) as $tag => $count)
                    <a href="{{ route('blog.index', array_merge(request()->except('page'), ['tag' => $tag])) }}" @class(['tag-chip', 'tag-chip--active' => $activeTag === $tag])>
                        <span>{{ $tag }}</span>
                        <strong>{{ $count }}</strong>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if ($blogPosts->count() > 0)
        <section class="blog-grid">
            @foreach ($blogPosts as $blogPost)
                <article class="blog-card">
                    @if ($blogPost->cover_image_url)
                        <img class="blog-card__image" src="{{ $blogPost->cover_image_url }}" alt="{{ $blogPost->titleForLocale() }}">
                    @else
                        <div class="blog-card__placeholder" aria-hidden="true"></div>
                    @endif

                    <div class="meta">
                        <span>{{ $blogPost->author?->name ?? __('hermes.blog.editorial_team') }}</span>
                        <span>{{ $blogPost->published_at?->translatedFormat('j M Y') }}</span>
                        <span>{{ __('hermes.blog.reading_time', ['minutes' => $blogPost->readingTimeInMinutes()]) }}</span>
                    </div>

                    <div>
                        <h3>{{ $blogPost->titleForLocale() }}</h3>
                        <p class="blog-card__excerpt">{{ $blogPost->excerptForLocale() }}</p>
                    </div>

                    <div class="tag-list">
                        @foreach ($blogPost->tagsList() as $tag)
                            <span>{{ $tag }}</span>
                        @endforeach
                    </div>

                    <div class="card-actions">
                        <a class="pill pill--strong" href="{{ route('blog.show', $blogPost) }}">{{ __('hermes.blog.read_post') }}</a>
                    </div>
                </article>
            @endforeach
        </section>

        <div class="pagination">
            {{ $blogPosts->links() }}
        </div>
    @else
        <section class="empty-state">
            <h2>{{ __('hermes.blog.empty_title') }}</h2>
            <p>{{ __('hermes.blog.empty_text') }}</p>
        </section>
    @endif
</x-layouts.hermes-public>
