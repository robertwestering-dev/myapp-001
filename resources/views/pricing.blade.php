<x-layouts.hermes-public
    :title="__('hermes.pricing_page.title')"
    :meta-description="__('hermes.pricing_page.meta_description')"
    :canonical-url="route('pricing.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="auth()->check()"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => __('hermes.pricing_page.title'),
        'description' => __('hermes.pricing_page.meta_description'),
        'url' => route('pricing.show'),
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
            .pricing-page,
            .pricing-hero,
            .pricing-hero__summary,
            .pricing-section,
            .pricing-grid,
            .pricing-card,
            .pricing-card__price,
            .pricing-list,
            .pricing-footnote {
                display: grid;
                gap: 24px;
            }

            .pricing-page {
                gap: 32px;
            }

            .pricing-hero,
            .pricing-card,
            .pricing-footnote {
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .pricing-hero {
                grid-template-columns: minmax(0, 1.16fr) minmax(280px, 0.84fr);
                padding: 32px;
                border-radius: 30px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.24), transparent 34%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 243, 234, 0.82));
            }

            .pricing-hero__summary,
            .pricing-card--accent {
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .pricing-hero__summary {
                align-content: start;
                padding: 24px;
                border-radius: 24px;
            }

            .pricing-hero__summary strong,
            .pricing-hero__summary span {
                display: block;
            }

            .pricing-hero__summary strong {
                color: rgba(248, 241, 231, 0.78);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.78rem;
            }

            .pricing-hero__summary span {
                font-size: 1.2rem;
                line-height: 1.6;
                color: #fff7ee;
            }

            .pricing-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .pricing-card,
            .pricing-footnote {
                padding: 28px;
                border-radius: 26px;
                background: rgba(255, 255, 255, 0.74);
            }

            .pricing-card--accent {
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
            }

            .pricing-card h3,
            .pricing-footnote h2 {
                margin: 0;
                font-size: clamp(1.25rem, 2vw, 1.65rem);
            }

            .pricing-card--accent h3,
            .pricing-card--accent .pricing-card__amount {
                color: #fff7ee;
            }

            .pricing-card p,
            .pricing-card li,
            .pricing-footnote p {
                margin: 0;
                line-height: 1.75;
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
            }

            .pricing-card--accent p,
            .pricing-card--accent li {
                color: rgba(248, 241, 231, 0.92);
            }

            .pricing-card__price {
                gap: 8px;
            }

            .pricing-card__label {
                font-size: 0.82rem;
                text-transform: uppercase;
                letter-spacing: 0.12em;
                color: var(--muted);
            }

            .pricing-card--accent .pricing-card__label {
                color: rgba(248, 241, 231, 0.78);
            }

            .pricing-card__amount {
                font-size: clamp(1.45rem, 2.4vw, 2rem);
                line-height: 1.2;
                font-weight: 700;
            }

            .pricing-list {
                gap: 12px;
                padding-left: 20px;
                margin: 0;
            }

            .pricing-section__lead {
                max-width: 72ch;
            }

            @media (max-width: 980px) {
                .pricing-hero,
                .pricing-grid {
                    grid-template-columns: 1fr;
                }

                .pricing-hero {
                    padding: 24px;
                }
            }
        </style>
    </x-slot:head>

    <div class="pricing-page">
        <section class="pricing-hero">
            <div>
                <x-user-page-heading
                    :eyebrow="__('hermes.pricing_page.eyebrow')"
                    :title="__('hermes.pricing_page.hero_title')"
                >
                    <x-slot:meta>
                        <div class="user-page-heading__meta">
                            <p>{{ __('hermes.pricing_page.hero_intro') }}</p>
                        </div>
                    </x-slot:meta>
                </x-user-page-heading>
            </div>

            <aside class="pricing-hero__summary">
                <strong>{{ __('hermes.pricing_page.summary_label') }}</strong>
                <span>{{ __('hermes.pricing_page.summary_text') }}</span>
            </aside>
        </section>

        <section class="pricing-section">
            <x-user-section-heading
                :eyebrow="__('hermes.pricing_page.personal_eyebrow')"
                :title="__('hermes.pricing_page.personal_title')"
                :text="__('hermes.pricing_page.personal_text')"
            />

            <div class="pricing-grid">
                <article class="pricing-card">
                    <h3>{{ __('hermes.pricing_page.personal_free.name') }}</h3>
                    <div class="pricing-card__price">
                        <span class="pricing-card__label">{{ __('hermes.pricing_page.price_label') }}</span>
                        <span class="pricing-card__amount">{{ __('hermes.pricing_page.personal_free.price') }}</span>
                    </div>
                    <p>{{ __('hermes.pricing_page.personal_free.tagline') }}</p>
                    <p>{{ __('hermes.pricing_page.access_to') }}</p>
                    <ul class="pricing-list">
                        @foreach (__('hermes.pricing_page.personal_free.features') as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <p>{{ __('hermes.pricing_page.personal_free.note') }}</p>
                    <div>
                        <a class="pill" href="{{ route('register') }}">{{ __('hermes.pricing_page.personal_free.cta') }}</a>
                    </div>
                </article>

                <article class="pricing-card pricing-card--accent">
                    <h3>{{ __('hermes.pricing_page.personal_pro.name') }}</h3>
                    <div class="pricing-card__price">
                        <span class="pricing-card__label">{{ __('hermes.pricing_page.price_label') }}</span>
                        <span class="pricing-card__amount">{{ __('hermes.pricing_page.personal_pro.price') }}</span>
                    </div>
                    <p>{{ __('hermes.pricing_page.personal_pro.tagline') }}</p>
                    <p>{{ __('hermes.pricing_page.everything_from_free') }}</p>
                    <ul class="pricing-list">
                        @foreach (__('hermes.pricing_page.personal_pro.features') as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <div>
                        <a class="pill pill--strong" href="{{ route('register') }}">{{ __('hermes.pricing_page.personal_pro.cta') }}</a>
                    </div>
                </article>
            </div>
        </section>

    </div>
</x-layouts.hermes-public>
