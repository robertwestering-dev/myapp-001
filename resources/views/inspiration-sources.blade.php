<x-layouts.hermes-public
    :title="__('hermes.inspiration_page.title')"
    :meta-description="__('hermes.inspiration_page.meta_description')"
    :canonical-url="route('inspiration-sources.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="false"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => __('hermes.inspiration_page.title'),
        'description' => __('hermes.inspiration_page.meta_description'),
        'url' => route('inspiration-sources.show'),
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
            .inspiration-page,
            .inspiration-hero,
            .inspiration-hero__summary,
            .inspiration-section,
            .inspiration-grid,
            .inspiration-card,
            .inspiration-cta {
                display: grid;
                gap: 24px;
            }

            .inspiration-page {
                gap: 32px;
            }

            .inspiration-hero,
            .inspiration-card,
            .inspiration-cta {
                min-width: 0;
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .inspiration-hero {
                grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
                padding: 32px;
                border-radius: 30px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.24), transparent 34%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 243, 234, 0.82));
            }

            .inspiration-hero__summary,
            .inspiration-cta {
                padding: 24px;
                border-radius: 24px;
                overflow: hidden;
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .inspiration-hero__summary strong,
            .inspiration-hero__summary span {
                display: block;
            }

            .inspiration-hero__summary strong {
                color: rgba(248, 241, 231, 0.78);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.78rem;
            }

            .inspiration-hero__summary span {
                font-size: 1.2rem;
                line-height: 1.6;
                color: #fff7ee;
            }

            .inspiration-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: start;
            }

            .inspiration-card {
                min-width: 0;
                padding: 28px;
                border-radius: 26px;
                background: rgba(255, 255, 255, 0.74);
                overflow: hidden;
            }

            .inspiration-card h2,
            .inspiration-card h3,
            .inspiration-cta h2 {
                margin: 0;
                text-wrap: balance;
            }

            .inspiration-card p,
            .inspiration-cta p {
                margin: 0;
                color: var(--muted);
                line-height: 1.8;
                font-family: Arial, Helvetica, sans-serif;
                overflow-wrap: anywhere;
                word-break: normal;
            }

            .inspiration-card p + p,
            .inspiration-cta p + p {
                margin-top: 12px;
            }

            .inspiration-card h3 + p {
                margin-top: 12px;
            }

            .inspiration-section .user-section-heading {
                gap: 12px;
            }

            .inspiration-card__book {
                color: var(--forest);
                font-weight: 700;
                overflow-wrap: anywhere;
            }

            .inspiration-cta h2,
            .inspiration-cta p {
                color: #fff7ee;
            }

            .inspiration-cta .pill {
                width: fit-content;
            }

            @media (max-width: 980px) {
                .inspiration-hero,
                .inspiration-grid {
                    grid-template-columns: 1fr;
                }

                .inspiration-hero {
                    padding: 24px;
                }
            }

            @media (max-width: 640px) {
                .inspiration-page {
                    gap: 24px;
                }

                .inspiration-section,
                .inspiration-grid,
                .inspiration-card,
                .inspiration-cta {
                    gap: 18px;
                }

                .inspiration-hero,
                .inspiration-card,
                .inspiration-cta {
                    border-radius: 24px;
                }

                .inspiration-hero,
                .inspiration-card,
                .inspiration-cta,
                .inspiration-hero__summary {
                    padding: 20px;
                }

                .inspiration-hero .user-page-heading,
                .inspiration-hero .user-page-heading__body,
                .inspiration-hero .user-page-heading__meta,
                .inspiration-section .user-section-heading {
                    gap: 10px;
                }

                .inspiration-hero .user-page-heading h1 {
                    font-size: clamp(1.55rem, 7vw, 2.05rem);
                    line-height: 1.02;
                }

                .inspiration-section .user-section-heading h2,
                .inspiration-card h3,
                .inspiration-cta h2 {
                    font-size: clamp(1.05rem, 5.4vw, 1.35rem);
                    line-height: 1.2;
                    overflow-wrap: anywhere;
                }

                .inspiration-hero .user-page-heading__meta p,
                .inspiration-section .user-section-heading p,
                .inspiration-card p,
                .inspiration-cta p {
                    font-size: 0.98rem;
                    line-height: 1.7;
                }

                .inspiration-card p + p,
                .inspiration-cta p + p {
                    margin-top: 10px;
                }

                .inspiration-card__book {
                    font-size: 0.94rem;
                    line-height: 1.5;
                }

                .inspiration-hero__summary span {
                    font-size: 1.05rem;
                    line-height: 1.5;
                }

                .inspiration-cta .pill {
                    width: 100%;
                    text-align: center;
                    white-space: normal;
                    word-break: break-word;
                }
            }
        </style>
    </x-slot:head>

    <div class="inspiration-page">
        <section class="inspiration-hero">
            <div>
                <x-user-page-heading
                    :eyebrow="__('hermes.inspiration_page.hero_eyebrow')"
                    :title="__('hermes.inspiration_page.hero_title')"
                >
                    <x-slot:meta>
                        <div class="user-page-heading__meta">
                            <p>{{ __('hermes.inspiration_page.hero_intro') }}</p>
                            <p>{{ __('hermes.inspiration_page.hero_follow_up') }}</p>
                        </div>
                    </x-slot:meta>
                </x-user-page-heading>
            </div>

            <aside class="inspiration-hero__summary">
                <strong>{{ __('hermes.inspiration_page.hero_summary_label') }}</strong>
                <span>{{ __('hermes.inspiration_page.hero_summary_value') }}</span>
            </aside>
        </section>

        <section class="inspiration-section">
            <x-user-section-heading
                :eyebrow="__('hermes.inspiration_page.pos_psych_eyebrow')"
                :title="__('hermes.inspiration_page.pos_psych_title')"
                :text="__('hermes.inspiration_page.pos_psych_text')"
            />

            <div class="inspiration-grid">
                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.seligman_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.seligman_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.seligman_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.seligman_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.dweck_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.dweck_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.dweck_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.dweck_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.duckworth_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.duckworth_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.duckworth_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.duckworth_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.reivich_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.reivich_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.reivich_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.reivich_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.rath_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.rath_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.rath_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.rath_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.csikszentmihalyi_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.csikszentmihalyi_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.csikszentmihalyi_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.csikszentmihalyi_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.fredrickson_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.fredrickson_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.fredrickson_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.fredrickson_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.bandura_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.bandura_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.bandura_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.bandura_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.snyder_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.snyder_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.snyder_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.snyder_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.oettingen_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.oettingen_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.oettingen_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.oettingen_book') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.oreilly_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.oreilly_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.oreilly_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.oreilly_book') }}</p>
                </article>
            </div>
        </section>

        <section class="inspiration-section">
            <x-user-section-heading
                :eyebrow="__('hermes.inspiration_page.learning_eyebrow')"
                :title="__('hermes.inspiration_page.learning_title')"
                :text="__('hermes.inspiration_page.learning_text')"
            />

            <div class="inspiration-grid">
                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.vandam_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.vandam_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.vandam_p2') }}</p>
                </article>
            </div>
        </section>

        <section class="inspiration-section">
            <x-user-section-heading
                :eyebrow="__('hermes.inspiration_page.philosophy_eyebrow')"
                :title="__('hermes.inspiration_page.philosophy_title')"
                :text="__('hermes.inspiration_page.philosophy_text')"
            />

            <div class="inspiration-grid">
                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.stoics_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.stoics_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.stoics_p2') }}</p>
                    <p>{{ __('hermes.inspiration_page.stoics_p3') }}</p>
                </article>

                <article class="inspiration-card">
                    <h3>{{ __('hermes.inspiration_page.frankl_title') }}</h3>
                    <p>{{ __('hermes.inspiration_page.frankl_p1') }}</p>
                    <p>{{ __('hermes.inspiration_page.frankl_p2') }}</p>
                    <p class="inspiration-card__book">{{ __('hermes.inspiration_page.frankl_book') }}</p>
                </article>
            </div>
        </section>

        <section class="inspiration-cta">
            <h2>{{ __('hermes.inspiration_page.cta_title') }}</h2>
            <p>{{ __('hermes.inspiration_page.cta_p1') }}</p>
            <p>{{ __('hermes.inspiration_page.cta_p2') }}</p>
            <a href="{{ route('register') }}" class="pill">{{ __('hermes.inspiration_page.cta_action') }}</a>
        </section>
    </div>
</x-layouts.hermes-public>
