<x-layouts.hermes-public :title="$blogPost->titleForLocale()">
    <x-slot:head>
        <style>
            .article-layout,
            .article-meta,
            .article-body,
            .article-side,
            .related-list,
            .related-card,
            .article-actions {
                display: grid;
                gap: 18px;
            }

            .article-layout {
                grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
                align-items: start;
            }

            .article-main,
            .article-side {
                padding: 30px;
                border-radius: var(--radius-xl);
                border: 1px solid rgba(255, 255, 255, 0.58);
                box-shadow: var(--shadow);
            }

            .article-main {
                background: rgba(255, 255, 255, 0.84);
            }

            .article-side {
                background: var(--panel);
            }

            .article-cover {
                width: 100%;
                aspect-ratio: 16 / 9;
                object-fit: cover;
                border-radius: 24px;
            }

            .article-cover--placeholder {
                width: 100%;
                aspect-ratio: 16 / 9;
                border-radius: 24px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.36), transparent 24%),
                    linear-gradient(135deg, rgba(188, 91, 44, 0.3), rgba(30, 71, 61, 0.92));
            }

            .article-meta {
                grid-auto-flow: column;
                justify-content: start;
                color: var(--muted);
                font-size: 0.95rem;
            }

            .article-tags {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .article-tags span {
                display: inline-flex;
                align-items: center;
                padding: 8px 12px;
                border-radius: 999px;
                background: rgba(30, 71, 61, 0.08);
                color: var(--forest);
                font-size: 0.85rem;
                font-weight: 600;
            }

            .article-body {
                color: var(--ink);
                line-height: 1.85;
            }

            .article-body h1,
            .article-body h2,
            .article-body h3 {
                margin: 28px 0 12px;
            }

            .article-body p,
            .article-body ul,
            .article-body ol,
            .article-body blockquote {
                margin: 0 0 18px;
            }

            .article-body ul,
            .article-body ol {
                padding-left: 20px;
            }

            .article-actions {
                grid-auto-flow: column;
                justify-content: start;
            }

            .related-card {
                padding: 18px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.72);
                border: 1px solid rgba(23, 35, 33, 0.08);
            }

            .related-card h3 {
                font-size: 1.2rem;
                margin-bottom: 8px;
            }

            .related-card p,
            .article-side p {
                color: var(--muted);
                line-height: 1.7;
            }

            @media (max-width: 1040px) {
                .article-layout {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <section class="article-layout">
        <article class="article-main">
            <span class="eyebrow">{{ __('hermes.blog.eyebrow') }}</span>
            <h1>{{ $blogPost->titleForLocale() }}</h1>

            <div class="article-meta">
                <span>{{ $blogPost->author?->name ?? __('hermes.blog.editorial_team') }}</span>
                <span>{{ $blogPost->published_at?->translatedFormat('j F Y') }}</span>
                <span>{{ __('hermes.blog.reading_time', ['minutes' => $blogPost->readingTimeInMinutes()]) }}</span>
            </div>

            <p>{{ $blogPost->excerptForLocale() }}</p>

            @if ($blogPost->cover_image_url)
                <img class="article-cover" src="{{ $blogPost->cover_image_url }}" alt="{{ $blogPost->titleForLocale() }}">
            @else
                <div class="article-cover--placeholder" aria-hidden="true"></div>
            @endif

            <div class="article-tags">
                @foreach ($blogPost->tagsList() as $tag)
                    <span>{{ $tag }}</span>
                @endforeach
            </div>

            <div class="article-body">
                {!! Illuminate\Support\Str::markdown($blogPost->contentForLocale()) !!}
            </div>

            <div class="article-actions">
                <a class="pill" href="{{ route('blog.index') }}">{{ __('hermes.blog.back_to_overview') }}</a>
                <a class="pill pill--strong" href="{{ route('home') }}#contact">{{ __('hermes.blog.contact_action') }}</a>
            </div>
        </article>

        <aside class="article-side">
            <span class="eyebrow">{{ __('hermes.blog.related_eyebrow') }}</span>
            <h2>{{ __('hermes.blog.related_heading') }}</h2>
            <p>{{ __('hermes.blog.related_intro') }}</p>

            <div class="related-list">
                @forelse ($relatedPosts as $relatedPost)
                    <article class="related-card">
                        <div class="article-meta">
                            <span>{{ $relatedPost->published_at?->translatedFormat('j M Y') }}</span>
                            <span>{{ __('hermes.blog.reading_time', ['minutes' => $relatedPost->readingTimeInMinutes()]) }}</span>
                        </div>

                        <h3>{{ $relatedPost->titleForLocale() }}</h3>
                        <p>{{ $relatedPost->excerptForLocale() }}</p>

                        <a class="pill" href="{{ route('blog.show', $relatedPost) }}">{{ __('hermes.blog.read_post') }}</a>
                    </article>
                @empty
                    <article class="related-card">
                        <h3>{{ __('hermes.blog.related_empty_title') }}</h3>
                        <p>{{ __('hermes.blog.related_empty_text') }}</p>
                    </article>
                @endforelse
            </div>
        </aside>
    </section>
</x-layouts.hermes-public>
