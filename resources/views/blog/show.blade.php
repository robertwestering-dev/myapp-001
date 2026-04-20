@php
    $isPreview = $isPreview ?? false;
    $articleUrl = $isPreview ? route('admin.blog-posts.preview', $blogPost) : $blogPost->publicUrl();
@endphp

<x-layouts.hermes-public
    :title="$blogPost->metaTitleForLocale()"
    :meta-description="$blogPost->metaDescriptionForLocale()"
    :canonical-url="$articleUrl"
    :meta-image="$blogPost->cover_image_url"
    :show-header-booking="false"
    :show-header-contact-link="auth()->check()"
    og-type="article"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $blogPost->titleForLocale(),
        'description' => $blogPost->metaDescriptionForLocale(),
        'image' => $blogPost->cover_image_url ? [$blogPost->cover_image_url] : [],
        'datePublished' => $blogPost->published_at?->toAtomString(),
        'dateModified' => $blogPost->updated_at?->toAtomString(),
        'author' => [
            '@type' => 'Person',
            'name' => $blogPost->author?->name ?? __('hermes.blog.editorial_team'),
        ],
        'mainEntityOfPage' => $articleUrl,
        'keywords' => implode(', ', $blogPost->tagsList()),
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
            }

            .article-cover {
                width: 100%;
                aspect-ratio: 16 / 9;
                object-fit: cover;
                border-radius: 24px;
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
            .article-body blockquote,
            .article-body .article-media {
                margin: 0 0 18px;
            }

            .article-body ul,
            .article-body ol {
                padding-left: 20px;
            }

            .article-body::after {
                content: "";
                display: block;
                clear: both;
            }

            .article-body .article-media {
                width: 100%;
            }

            .article-body .article-media img,
            .article-body .article-media video {
                width: 100%;
                display: block;
                border-radius: 16px;
            }

            .article-body .article-media--left {
                float: left;
                margin: 6px 24px 18px 0;
            }

            .article-body .article-media--right {
                float: right;
                margin: 6px 0 18px 24px;
            }

            .article-body .article-media--center {
                margin-left: auto;
                margin-right: auto;
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

                .article-body .article-media--left,
                .article-body .article-media--right {
                    float: none;
                    margin-left: auto;
                    margin-right: auto;
                }
            }
        </style>
    </x-slot:head>

    <section class="article-layout">
        <x-user-surface-card class="article-main">
            <x-user-page-heading
                :eyebrow="__('hermes.blog.eyebrow')"
                :title="$blogPost->titleForLocale()"
            >
                <x-slot:meta>
                    <x-user-inline-meta
                        :items="[
                            $blogPost->author?->name ?? __('hermes.blog.editorial_team'),
                            $blogPost->published_at?->translatedFormat('j F Y'),
                            __('hermes.blog.reading_time', ['minutes' => $blogPost->readingTimeInMinutes()]),
                        ]"
                    />
                </x-slot:meta>
            </x-user-page-heading>
            @if ($isPreview)
                <div class="pill">{{ __('hermes.blog.preview_notice') }}</div>
            @endif

            <p>{{ $blogPost->excerptForLocale() }}</p>

            @if ($blogPost->cover_image_url)
                <img class="article-cover" src="{{ $blogPost->cover_image_url }}" alt="{{ $blogPost->titleForLocale() }}">
            @endif

            <div class="article-tags">
                @foreach ($blogPost->tagsList() as $tag)
                    <a class="pill" href="{{ route('blog.index', ['tag' => $tag]) }}">{{ $tag }}</a>
                @endforeach
            </div>

            <div class="article-body">
                {!! $blogPost->renderedContentForLocale() !!}
            </div>

            <x-user-action-row class="article-actions">
                <a class="pill" href="{{ $blogIndexUrl }}">{{ __('hermes.blog.back_to_overview') }}</a>
            </x-user-action-row>
        </x-user-surface-card>

        <x-user-surface-card tag="aside" class="article-side">
            <x-user-section-heading
                :eyebrow="__('hermes.blog.related_eyebrow')"
                :title="__('hermes.blog.related_heading')"
                :text="__('hermes.blog.related_intro')"
            />

            <div class="related-list">
                @forelse ($relatedPosts as $relatedPost)
                    <x-user-surface-card class="related-card">
                        <x-user-inline-meta
                            :items="[
                                $relatedPost->published_at?->translatedFormat('j M Y'),
                                __('hermes.blog.reading_time', ['minutes' => $relatedPost->readingTimeInMinutes()]),
                            ]"
                        />

                        <h3>{{ $relatedPost->titleForLocale() }}</h3>
                        <p>{{ $relatedPost->excerptForLocale() }}</p>

                        <x-user-action-row>
                            <a class="pill" href="{{ route('blog.show', $relatedPost) }}">{{ __('hermes.blog.read_post') }}</a>
                        </x-user-action-row>
                    </x-user-surface-card>
                @empty
                    <x-user-guidance-card
                        class="related-card"
                        :eyebrow="__('hermes.blog.related_eyebrow')"
                        :title="__('hermes.blog.related_empty_title')"
                        :text="__('hermes.blog.related_empty_text')"
                    />
                @endforelse
            </div>
        </x-user-surface-card>
    </section>
</x-layouts.hermes-public>
