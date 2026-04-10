<x-layouts.hermes-public
    :title="__('hermes.home_people.title')"
    :meta-description="__('hermes.home_people.meta_description')"
    :canonical-url="route('home')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="false"
    :show-header-contact-link="false"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'WebSite',
                'name' => 'Hermes Results',
                'url' => route('home'),
                'description' => __('hermes.home_people.meta_description'),
                'inLanguage' => app()->getLocale(),
            ],
            [
                '@type' => 'Organization',
                'name' => 'Hermes Results',
                'url' => route('home'),
                'logo' => asset('images/hermes-results-logo.png'),
                'description' => __('hermes.home_people.meta_description'),
            ],
        ],
    ]"
>
    <x-slot:head>
        <style>
            :root {
                --business-accent: #58748a;
                --business-accent-deep: #3f5c71;
            }

            .home-page,
            .home-hero,
            .home-hero__stats,
            .home-actions,
            .home-card-grid,
            .home-tool-grid,
            .home-confidence-grid,
            .home-organization-card,
            .contact-form,
            .contact-form form,
            .contact-form label,
            .checkbox-field {
                display: grid;
                gap: 20px;
            }

            .home-page {
                gap: 24px;
            }

            .home-hero,
            .home-section {
                padding: 28px;
                border-radius: 30px;
                border: 1px solid rgba(23, 35, 33, 0.1);
                box-shadow: var(--shadow);
            }

            .home-hero {
                grid-template-columns: minmax(0, 1.2fr) minmax(280px, 0.8fr);
                align-items: start;
                background:
                    radial-gradient(circle at top right, rgba(214, 179, 122, 0.26), transparent 30%),
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98));
                color: #f8f1e7;
            }

            .home-hero > :first-child,
            .home-hero .user-page-heading,
            .home-hero .user-page-heading__body {
                align-self: start;
                justify-content: start;
            }

            .home-hero h1,
            .home-strip h2 {
                color: inherit;
            }

            .home-hero .user-page-heading__eyebrow,
            .home-hero .user-page-heading p,
            .home-hero .user-page-heading__meta {
                color: rgba(248, 241, 231, 0.84);
            }

            .home-hero .user-page-heading h1 {
                color: #f8f1e7;
                font-size: clamp(1.72rem, 3.45vw, 3.15rem);
                line-height: 0.98;
                max-width: none;
            }

            .home-hero__aside {
                display: grid;
                gap: 18px;
                align-content: start;
            }

            .home-hero__stats {
                grid-template-columns: 1fr;
                margin-top: 8px;
            }

            .home-actions {
                grid-auto-flow: column;
                justify-content: start;
                gap: 14px;
            }

            .home-actions .pill {
                width: fit-content;
            }

            .home-card-grid,
            .home-tool-grid,
            .home-confidence-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .home-tool-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .home-section {
                background: rgba(255, 255, 255, 0.72);
            }

            .home-section p,
            .contact-form label span,
            .checkbox-field__text {
                margin: 0;
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.7;
            }

            .home-section h3,
            .contact-panel h2 {
                margin-bottom: 10px;
            }

            .home-section ul,
            .organization-list {
                margin: 0;
                padding-left: 20px;
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.7;
            }

            .home-section li + li,
            .organization-list li + li {
                margin-top: 8px;
            }

            .home-section h3 + p,
            .home-section .user-section-heading p,
            .home-section .user-page-heading__meta p:last-child,
            .home-organization-card p {
                margin-bottom: 12px;
            }

            .home-section--warm {
                background:
                    radial-gradient(circle at top left, rgba(188, 91, 44, 0.16), transparent 22%),
                    rgba(255, 255, 255, 0.72);
            }

            .home-organization-card .pill {
                background: linear-gradient(180deg, rgba(222, 229, 235, 0.98), rgba(210, 220, 228, 0.98));
                border-color: rgba(88, 116, 138, 0.3);
                color: #294456;
                box-shadow: 0 10px 24px rgba(63, 92, 113, 0.12);
                font-weight: 700;
            }

            .home-organization-card .pill:hover {
                background: linear-gradient(180deg, rgba(230, 236, 241, 1), rgba(218, 227, 235, 1));
                border-color: rgba(88, 116, 138, 0.4);
            }

            .checkbox-field {
                gap: 10px;
                align-items: start;
            }

            .checkbox-field label {
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }

            .checkbox-field input[type="checkbox"] {
                width: 18px;
                height: 18px;
                margin-top: 2px;
                padding: 0;
            }

            .form-status,
            .field-error {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.95rem;
            }

            .form-status {
                padding: 14px 16px;
                border-radius: 16px;
                background: rgba(30, 71, 61, 0.1);
                color: var(--forest);
            }

            .field-error {
                color: #a63d1b;
            }

            @media (max-width: 980px) {
                .home-hero,
                .home-tool-grid,
                .home-card-grid,
                .home-confidence-grid,
                .home-hero__stats {
                    grid-template-columns: 1fr;
                }

                .home-actions {
                    grid-auto-flow: row;
                }
            }
        </style>
    </x-slot:head>

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

    <div class="home-page">
        <section class="home-hero">
            <x-user-page-heading
                :eyebrow="__('hermes.home_people.eyebrow')"
                :title="__('hermes.home_people.hero_title')"
            >
                <x-slot:meta>
                    <div class="user-page-heading__meta">
                        <p>{{ __('hermes.home_people.hero_intro') }}</p>
                        <p>{{ __('hermes.home_people.hero_intro_extra') }}</p>
                    </div>

                    <div class="home-actions">
                        <a class="pill pill--strong" href="{{ route('register') }}">{{ __('hermes.home_people.hero_primary') }}</a>
                    </div>
                </x-slot:meta>
            </x-user-page-heading>

            <div class="home-hero__aside">
                <div class="home-hero__stats">
                    <x-user-stat-tile
                        :value="__('hermes.home_people.stat_1_value')"
                        :label="__('hermes.home_people.stat_1_label')"
                    />
                    <x-user-stat-tile
                        :value="__('hermes.home_people.stat_3_value')"
                        :label="__('hermes.home_people.stat_3_label')"
                    />
                </div>
            </div>
        </section>

        <section class="home-section">
            <x-user-section-heading
                :eyebrow="__('hermes.home_people.challenges_eyebrow')"
                :title="__('hermes.home_people.path_title')"
                :text="__('hermes.home_people.path_text')"
            />

            <div class="home-card-grid">
                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.challenge_1_title') }}</h3>
                    <p>{{ __('hermes.home_people.challenge_1_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.challenge_2_title') }}</h3>
                    <p>{{ __('hermes.home_people.challenge_2_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.challenge_3_title') }}</h3>
                    <p>{{ __('hermes.home_people.challenge_3_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.challenge_4_title') }}</h3>
                    <p>{{ __('hermes.home_people.challenge_4_text') }}</p>
                </x-user-surface-card>
            </div>
        </section>

        <section id="diensten" class="home-section">
            <x-user-section-heading
                :eyebrow="__('hermes.home_people.tools_eyebrow')"
                :title="__('hermes.home_people.tools_title')"
                :text="__('hermes.home_people.tools_text')"
            />

            <div class="home-tool-grid">
                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.tool_1_title') }}</h3>
                    <p>{{ __('hermes.home_people.tool_1_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.tool_2_title') }}</h3>
                    <p>{{ __('hermes.home_people.tool_2_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.tool_3_title') }}</h3>
                    <p>{{ __('hermes.home_people.tool_3_text') }}</p>
                </x-user-surface-card>
            </div>
        </section>

        <section class="home-section home-section--warm">
            <x-user-section-heading
                :eyebrow="__('hermes.home_people.confidence_eyebrow')"
                :title="__('hermes.home_people.confidence_title')"
                :text="__('hermes.home_people.confidence_text')"
            />

            <div class="home-confidence-grid">
                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.confidence_1_title') }}</h3>
                    <p>{{ __('hermes.home_people.confidence_1_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.confidence_2_title') }}</h3>
                    <p>{{ __('hermes.home_people.confidence_2_text') }}</p>
                </x-user-surface-card>

                <x-user-surface-card variant="soft" class="home-section">
                    <h3>{{ __('hermes.home_people.confidence_3_title') }}</h3>
                    <p>{{ __('hermes.home_people.confidence_3_text') }}</p>
                </x-user-surface-card>
            </div>
        </section>

        <section class="home-section home-organization-card">
            <x-user-section-heading
                eyebrow="Achtergrond en visie"
                title="Nieuwsgierig naar de denkers achter Hermes Results?"
                text="Lees op welke wetenschappers, filosofen en praktijkdenkers ons model is gebouwd en waarom juist hun inzichten onze aanpak richting geven."
            />

            <div class="home-actions">
                <a href="{{ route('inspiration-sources.show') }}" class="pill">Bekijk de inspiratiebronnen</a>
            </div>
        </section>

        <section class="home-section home-organization-card">
            <x-user-section-heading
                :eyebrow="__('hermes.home_people.organization_eyebrow')"
                :title="__('hermes.home_people.organization_title')"
                :text="__('hermes.home_people.organization_text')"
            />

            <div class="home-actions">
                <a href="{{ route('organizations.landing') }}" class="pill">{{ __('hermes.home_people.organization_action') }}</a>
            </div>
        </section>

    </div>
</x-layouts.hermes-public>
