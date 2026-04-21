<x-layouts.hermes-public
    :title="__('hermes.pro_upgrade_page.title')"
    :meta-description="__('hermes.pro_upgrade_page.meta_description')"
    :canonical-url="route('pro-upgrade.show')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="false"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => __('hermes.pro_upgrade_page.title'),
        'description' => __('hermes.pro_upgrade_page.meta_description'),
        'url' => route('pro-upgrade.show'),
    ]"
>
    @guest
        <x-slot:headerMenu>
            <a class="home-menu-item" href="{{ route('home') }}">Home</a>
            <a class="home-menu-item" href="{{ route('blog.index') }}">Blog</a>
            <details class="home-menu-dropdown">
                <summary class="home-menu-trigger">
                    Over
                    <span aria-hidden="true">v</span>
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
            .pro-upgrade-page,
            .pro-upgrade-card,
            .pro-upgrade-card__price,
            .pro-upgrade-list {
                display: grid;
                gap: 24px;
            }

            .pro-upgrade-page {
                gap: 32px;
                max-width: 780px;
                margin: 0 auto;
            }

            .pro-upgrade-card {
                padding: 32px;
                border-radius: 26px;
                border: 1px solid rgba(255, 255, 255, 0.12);
                box-shadow: var(--shadow);
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .pro-upgrade-card h3 {
                margin: 0;
                color: #fff7ee;
                font-size: clamp(1.35rem, 2vw, 1.75rem);
            }

            .pro-upgrade-card p,
            .pro-upgrade-card li {
                margin: 0;
                color: rgba(248, 241, 231, 0.92);
                font-family: Arial, Helvetica, sans-serif;
                line-height: 1.75;
            }

            .pro-upgrade-card__price {
                gap: 8px;
            }

            .pro-upgrade-card__label {
                color: rgba(248, 241, 231, 0.78);
                font-size: 0.82rem;
                text-transform: uppercase;
                letter-spacing: 0.12em;
            }

            .pro-upgrade-card__amount {
                color: #fff7ee;
                font-size: clamp(1.45rem, 2.4vw, 2rem);
                line-height: 1.2;
                font-weight: 700;
            }

            .pro-upgrade-card__amount s {
                text-decoration-thickness: 0.12em;
            }

            .pro-upgrade-list {
                gap: 12px;
                padding-left: 20px;
                margin: 0;
            }

            .pro-upgrade-form {
                margin: 0;
            }

            @media (max-width: 720px) {
                .pro-upgrade-card {
                    padding: 24px;
                }
            }
        </style>
    </x-slot:head>

    <div class="pro-upgrade-page">
        <x-user-page-heading
            :eyebrow="__('hermes.pro_upgrade_page.eyebrow')"
            :title="__('hermes.pro_upgrade_page.hero_title')"
        >
            <x-slot:meta>
                <div class="user-page-heading__meta">
                    <p>{{ __('hermes.pro_upgrade_page.hero_intro') }}</p>
                </div>
            </x-slot:meta>
        </x-user-page-heading>

        <article class="pro-upgrade-card">
            <h3>{{ __('hermes.pricing_page.personal_pro.name') }}</h3>
            <div class="pro-upgrade-card__price">
                <span class="pro-upgrade-card__label">{{ __('hermes.pricing_page.price_label') }}</span>
                <span class="pro-upgrade-card__amount"><s>{{ __('hermes.pricing_page.personal_pro.price') }}</s> <b>{{ __('hermes.pro_upgrade_page.free_label') }}</b></span>
            </div>
            <p>{{ __('hermes.pro_upgrade_page.temporary_tagline') }}</p>
            <p>{{ __('hermes.pricing_page.everything_from_free') }}</p>
            <ul class="pro-upgrade-list">
                @foreach (__('hermes.pricing_page.personal_pro.features') as $feature)
                    <li>{{ $feature }}</li>
                @endforeach
            </ul>

            @if (session('status'))
                <x-user-feedback variant="status" :messages="[session('status')]" />
            @endif

            @auth
                <form method="POST" action="{{ route('pro-upgrade.store') }}" class="pro-upgrade-form">
                    @csrf
                    <button type="submit" class="pill pill--strong">{{ __('hermes.pricing_page.personal_pro.cta') }}</button>
                </form>
            @else
                <div>
                    <a class="pill pill--strong" href="{{ route('register') }}">{{ __('hermes.pricing_page.personal_pro.cta') }}</a>
                </div>
            @endauth
        </article>
    </div>
</x-layouts.hermes-public>
