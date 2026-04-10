<x-layouts.hermes-public
    :title="__('hermes.about_page.title')"
    :meta-description="__('hermes.about_page.meta_description')"
    :canonical-url="route('about.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="auth()->check()"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'AboutPage',
        'name' => __('hermes.about_page.title'),
        'description' => __('hermes.about_page.meta_description'),
        'url' => route('about.show'),
    ]"
>
    @guest
        <x-slot:headerMenu>
            <a class="home-menu-item" href="{{ route('home') }}">Home</a>
            <a class="home-menu-item" href="{{ route('blog.index') }}">Blog</a>
            <div class="home-menu-dropdown">
                <a class="home-menu-trigger" href="{{ route('about.show') }}">
                    Over
                    <span aria-hidden="true">▾</span>
                </a>
                <div class="home-submenu">
                    <a href="{{ route('inspiration-sources.show') }}">Inspiratiebronnen</a>
                    <a href="{{ route('about.show') }}">Over ons</a>
                    <a href="{{ route('pricing.show') }}">Prijzen</a>
                    <a href="{{ route('privacy.show') }}">{{ __('hermes.footer.privacy') }}</a>
                </div>
            </div>
            <a class="home-menu-item" href="{{ route('organizations.landing') }}">Organisaties</a>
        </x-slot:headerMenu>
    @endguest

    <x-slot:head>
        <style>
            .about-page,
            .about-hero,
            .about-hero__content,
            .about-hero__summary,
            .about-story,
            .about-story__card {
                display: grid;
                gap: 24px;
            }

            .about-page {
                gap: 32px;
            }

            .about-hero,
            .about-story__card {
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .about-hero {
                grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
                padding: 32px;
                border-radius: 30px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.24), transparent 34%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 243, 234, 0.82));
            }

            .about-hero p,
            .about-story__card p {
                margin: 0;
                color: var(--muted);
                line-height: 1.8;
                font-family: Arial, Helvetica, sans-serif;
            }

            .about-hero__summary {
                align-content: start;
                padding: 24px;
                border-radius: 24px;
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .about-hero__summary strong,
            .about-hero__summary span {
                display: block;
            }

            .about-hero__summary strong {
                color: rgba(248, 241, 231, 0.78);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.78rem;
            }

            .about-hero__summary span {
                font-size: 1.3rem;
                line-height: 1.5;
                color: #fff7ee;
            }

            .about-story {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .about-story__card {
                padding: 28px;
                border-radius: 26px;
                background: rgba(255, 255, 255, 0.74);
            }

            .about-story__card--accent {
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .about-story__card--accent p {
                color: rgba(248, 241, 231, 0.9);
            }

            .about-story__card h2 {
                margin: 0;
                font-size: clamp(1.35rem, 2vw, 1.75rem);
            }

            @media (max-width: 920px) {
                .about-hero,
                .about-story {
                    grid-template-columns: 1fr;
                }

                .about-hero {
                    padding: 24px;
                }
            }
        </style>
    </x-slot:head>

    <div class="about-page">
        <section class="about-hero">
            <div class="about-hero__content">
                <x-user-page-heading
                    :eyebrow="__('hermes.about_page.eyebrow')"
                    :title="__('hermes.about_page.hero_title')"
                >
                    <x-slot:meta>
                        <div class="user-page-heading__meta">
                            <p>{{ __('hermes.about_page.hero_intro') }}</p>
                            <p>{{ __('hermes.about_page.hero_follow_up') }}</p>
                        </div>
                    </x-slot:meta>
                </x-user-page-heading>
            </div>

            <aside class="about-hero__summary">
                <strong>{{ __('hermes.about_page.summary_label') }}</strong>
                <span>{{ __('hermes.about_page.summary_value') }}</span>
            </aside>
        </section>

        <section class="about-story">
            <article class="about-story__card">
                <h2>{{ __('hermes.about_page.story_title') }}</h2>

                @foreach (__('hermes.about_page.story_paragraphs') as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </article>

            <article class="about-story__card about-story__card--accent">
                <h2>{{ __('hermes.about_page.mission_title') }}</h2>

                @foreach (__('hermes.about_page.mission_paragraphs') as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </article>
        </section>
    </div>
</x-layouts.hermes-public>
