<x-layouts.hermes-public
    :title="__('hermes.privacy.title')"
    :meta-description="__('hermes.privacy.meta_description')"
    :canonical-url="route('privacy.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="false"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => __('hermes.privacy.hero_title'),
        'description' => __('hermes.privacy.meta_description'),
        'url' => route('privacy.show'),
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
            .privacy-page,
            .privacy-hero,
            .privacy-summary,
            .privacy-grid,
            .privacy-sections,
            .privacy-section,
            .privacy-section__content {
                display: grid;
                gap: 24px;
            }

            .privacy-page {
                gap: 28px;
            }

            .privacy-hero,
            .privacy-summary article,
            .privacy-section {
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .privacy-hero {
                grid-template-columns: minmax(0, 1.12fr) minmax(280px, 0.88fr);
                padding: 30px;
                border-radius: 30px;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.22), transparent 32%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 243, 234, 0.82));
            }

            .privacy-hero__meta,
            .privacy-updated,
            .privacy-summary {
                display: grid;
                gap: 16px;
            }

            .privacy-updated {
                align-content: start;
                padding: 24px;
                border-radius: 24px;
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .privacy-updated strong,
            .privacy-updated p,
            .privacy-section p,
            .privacy-section li {
                margin: 0;
                line-height: 1.75;
            }

            .privacy-updated strong {
                color: rgba(248, 241, 231, 0.78);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.78rem;
            }

            .privacy-updated p {
                font-size: 1.25rem;
                color: #fff7ee;
            }

            .privacy-summary {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .privacy-summary article,
            .privacy-section {
                padding: 24px;
                border-radius: 24px;
                background: rgba(255, 255, 255, 0.72);
            }

            .privacy-summary h2,
            .privacy-section h2 {
                margin-bottom: 10px;
                font-size: 1.12rem;
            }

            .privacy-summary p,
            .privacy-section p,
            .privacy-section li {
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
            }

            .privacy-grid {
                grid-template-columns: minmax(0, 1.22fr) minmax(300px, 0.78fr);
                align-items: start;
            }

            .privacy-sections {
                gap: 18px;
            }

            .privacy-section ul {
                margin: 0;
                padding-left: 20px;
            }

            .privacy-section li + li,
            .privacy-section p + p,
            .privacy-section p + ul,
            .privacy-section ul + p {
                margin-top: 10px;
            }

            .privacy-section__note {
                padding: 16px 18px;
                border-radius: 18px;
                background: rgba(30, 71, 61, 0.08);
                color: var(--forest-deep);
            }

            @media (max-width: 960px) {
                .privacy-hero,
                .privacy-grid,
                .privacy-summary {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </x-slot:head>

    <div class="privacy-page">
        <section class="privacy-hero">
            <div class="privacy-hero__meta">
                <x-user-page-heading
                    :eyebrow="__('hermes.privacy.eyebrow')"
                    :title="__('hermes.privacy.hero_title')"
                    :text="__('hermes.privacy.hero_intro')"
                />
            </div>

            <aside class="privacy-updated">
                <strong>{{ __('hermes.privacy.updated_label') }}</strong>
                <p>{{ __('hermes.privacy.updated_value') }}</p>
            </aside>
        </section>

        <section class="privacy-summary">
            <x-user-surface-card variant="soft">
                <h2>{{ __('hermes.privacy.summary_1_title') }}</h2>
                <p>{{ __('hermes.privacy.summary_1_text') }}</p>
            </x-user-surface-card>

            <x-user-surface-card variant="soft">
                <h2>{{ __('hermes.privacy.summary_2_title') }}</h2>
                <p>{!! __('hermes.privacy.summary_2_text') !!}</p>
            </x-user-surface-card>

            <x-user-surface-card variant="soft">
                <h2>{{ __('hermes.privacy.summary_3_title') }}</h2>
                <p>{{ __('hermes.privacy.summary_3_text') }}</p>
            </x-user-surface-card>
        </section>

        <section class="privacy-grid">
            <div class="privacy-sections">
                @foreach (__('hermes.privacy.sections') as $section)
                    <article class="privacy-section">
                        <h2>{{ $section['title'] }}</h2>

                        <div class="privacy-section__content">
                            @foreach ($section['paragraphs'] ?? [] as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach

                            @if (! empty($section['bullets']))
                                <ul>
                                    @foreach ($section['bullets'] as $bullet)
                                        <li>{{ $bullet }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if (! empty($section['note']))
                                <p class="privacy-section__note">{{ $section['note'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <x-user-guidance-card
                variant="accent"
                :eyebrow="__('hermes.privacy.contact_card_eyebrow')"
                :title="__('hermes.privacy.contact_card_title')"
                :text="__('hermes.privacy.contact_card_text')"
                :action-label="__('hermes.privacy.contact_card_action')"
                :action-href="route('contact.show').'#contact'"
            />
        </section>
    </div>
</x-layouts.hermes-public>
