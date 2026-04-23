<x-layouts.hermes-public
    :title="__('hermes.organizations_page.title')"
    :meta-description="__('hermes.organizations_page.meta_description')"
    :canonical-url="route('organizations.landing')"
    :meta-image="asset('images/hermes-results-logo.png')"
    :show-header-booking="false"
    :show-header-contact-link="auth()->check()"
    :structured-data="[
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => __('hermes.organizations_page.heading'),
        'description' => __('hermes.organizations_page.meta_description'),
        'url' => route('organizations.landing'),
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
            .organization-legacy-page {
                display: grid;
                gap: 48px;
            }

            .organization-legacy-page h1,
            .organization-legacy-page h2,
            .organization-legacy-page h3,
            .organization-legacy-page p {
                margin-top: 0;
            }

            .organization-legacy-page h1,
            .organization-legacy-page h2,
            .organization-legacy-page h3 {
                font-family: "Georgia", "Times New Roman", serif;
                letter-spacing: -0.02em;
            }

            .organization-legacy-page h1 {
                margin: 20px 0 18px;
                font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
                line-height: 0.96;
                max-width: none;
            }

            .organization-legacy-page section + section {
                padding-top: 20px;
            }

            .organization-legacy-page .hero {
                display: grid;
                grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
                gap: 24px;
                align-items: stretch;
            }

            .organization-legacy-page .hero__panel,
            .organization-legacy-page .hero__sidebar,
            .organization-legacy-page .problem__grid article,
            .organization-legacy-page .offer-card,
            .organization-legacy-page .plan__step,
            .organization-legacy-page .bridge__panel,
            .organization-legacy-page .closing__panel {
                min-width: 0;
                border: 1px solid rgba(255, 255, 255, 0.58);
                box-shadow: var(--shadow);
            }

            .organization-legacy-page .hero__panel,
            .organization-legacy-page .hero__sidebar,
            .organization-legacy-page .bridge__panel,
            .organization-legacy-page .closing__panel {
                overflow: hidden;
                position: relative;
            }

            .organization-legacy-page .hero__panel {
                padding: 42px;
                border-radius: var(--radius-xl);
                background:
                    radial-gradient(circle at 82% 24%, rgba(214, 179, 122, 0.28), transparent 24%),
                    linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(250, 243, 234, 0.82));
            }

            .organization-legacy-page .hero__panel::after {
                content: "";
                position: absolute;
                right: -50px;
                bottom: -100px;
                width: 240px;
                height: 240px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(188, 91, 44, 0.18), transparent 72%);
                pointer-events: none;
            }

            .organization-legacy-page .hero__sidebar,
            .organization-legacy-page .bridge__panel {
                padding: 30px;
                border-radius: var(--radius-xl);
                background:
                    linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                    var(--forest);
                color: #f8f1e7;
            }

            .organization-legacy-page .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 8px 14px;
                border-radius: 999px;
                background: rgba(30, 71, 61, 0.08);
                color: var(--forest);
                text-transform: uppercase;
                letter-spacing: 0.11em;
                font-size: 0.78rem;
                font-weight: 700;
            }

            .organization-legacy-page .eyebrow--light {
                background: rgba(255, 255, 255, 0.08);
                color: rgba(248, 241, 231, 0.86);
            }

            .organization-legacy-page .hero__intro {
                max-width: 58ch;
                font-size: 1.08rem;
                line-height: 1.8;
                color: var(--muted);
                margin-bottom: 26px;
            }

            .organization-legacy-page .hero__actions,
            .organization-legacy-page .offer-card__footer,
            .organization-legacy-page .nav-actions {
                display: flex;
                gap: 14px;
                flex-wrap: wrap;
                align-items: center;
            }

            .organization-legacy-page .hero__actions {
                margin-bottom: 28px;
            }

            .organization-legacy-page .hero__proof {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
            }

            .organization-legacy-page .hero__proof article {
                padding: 18px;
                border-radius: var(--radius-md);
                background: rgba(255, 255, 255, 0.62);
                border: 1px solid rgba(23, 35, 33, 0.08);
            }

            .organization-legacy-page .hero__proof strong {
                display: block;
                margin-bottom: 6px;
                font-size: 1.02rem;
            }

            .organization-legacy-page .hero__proof span,
            .organization-legacy-page .offer-card p,
            .organization-legacy-page .offer-card li,
            .organization-legacy-page .plan__step p,
            .organization-legacy-page .closing__panel p,
            .organization-legacy-page .problem__grid p {
                color: var(--muted);
                line-height: 1.7;
                overflow-wrap: anywhere;
            }

            .organization-legacy-page .hero__sidebar h2,
            .organization-legacy-page .bridge__panel h2 {
                font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
                line-height: 1.05;
                margin: 14px 0 18px;
                color: #fff7ee;
            }

            .organization-legacy-page .hero__sidebar p,
            .organization-legacy-page .bridge__panel p,
            .organization-legacy-page .hero__sidebar li {
                color: rgba(248, 241, 231, 0.84);
                line-height: 1.8;
            }

            .organization-legacy-page .hero__sidebar ul,
            .organization-legacy-page .offer-card ul {
                margin: 0;
                padding-left: 18px;
            }

            .organization-legacy-page .hero__sidebar .sidebar-box,
            .organization-legacy-page .bridge__highlight {
                margin-top: 22px;
                padding: 18px;
                border-radius: var(--radius-md);
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .organization-legacy-page .section-head {
                display: block;
                width: 100%;
                margin-bottom: 18px;
            }

            .organization-legacy-page .section-head h2 {
                margin-bottom: 0;
                font-size: clamp(1.5rem, 2.4vw, var(--block-heading-max));
            }

            .organization-legacy-page .tagline {
                margin-bottom: 10px;
                color: var(--clay-deep);
                text-transform: uppercase;
                letter-spacing: 0.12em;
                font-size: 0.8rem;
                font-weight: 700;
            }

            .organization-legacy-page .problem__grid,
            .organization-legacy-page .offer-grid,
            .organization-legacy-page .plan__grid {
                display: grid;
                gap: 18px;
            }

            .organization-legacy-page .problem__grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .organization-legacy-page .problem__grid article,
            .organization-legacy-page .offer-card,
            .organization-legacy-page .plan__step,
            .organization-legacy-page .closing__panel,
            .organization-legacy-page .contact-form {
                border-radius: var(--radius-lg);
                background: var(--panel);
            }

            .organization-legacy-page .problem__grid article {
                padding: 24px;
            }

            .organization-legacy-page .problem__grid h3,
            .organization-legacy-page .offer-card h3,
            .organization-legacy-page .plan__step h3 {
                margin-bottom: 10px;
                font-size: 1.35rem;
            }

            .organization-legacy-page .offer-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .organization-legacy-page .offer-card {
                padding: 24px;
                display: flex;
                flex-direction: column;
                gap: 18px;
                min-width: 0;
            }

            .organization-legacy-page .offer-card--featured {
                background:
                    linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 236, 222, 0.94));
                transform: translateY(-4px);
            }

            .organization-legacy-page .offer-card__meta {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                font-size: 0.88rem;
                color: var(--muted);
            }

            .organization-legacy-page .offer-badge {
                display: inline-flex;
                align-items: center;
                padding: 7px 12px;
                border-radius: 999px;
                background: rgba(188, 91, 44, 0.12);
                color: var(--clay-deep);
                font-weight: 700;
            }

            .organization-legacy-page .offer-note {
                font-size: 0.92rem;
                color: var(--muted);
            }

            .organization-legacy-page .plan__grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .organization-legacy-page .plan__step {
                padding: 24px;
                min-width: 0;
            }

            .organization-legacy-page .plan__number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 42px;
                height: 42px;
                border-radius: 999px;
                margin-bottom: 16px;
                background: rgba(188, 91, 44, 0.13);
                color: var(--clay-deep);
                font-weight: 800;
            }

            .organization-legacy-page .bridge__panel {
                display: grid;
                grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
                gap: 24px;
                align-items: center;
            }

            .organization-legacy-page .bridge__list {
                display: grid;
                gap: 12px;
            }

            .organization-legacy-page .failure__panel {
                padding: 30px;
                border-radius: var(--radius-xl);
                border: 1px solid rgba(255, 255, 255, 0.58);
                background:
                    radial-gradient(circle at top right, rgba(188, 91, 44, 0.16), transparent 26%),
                    rgba(255, 255, 255, 0.72);
                box-shadow: var(--shadow);
            }

            .organization-legacy-page .failure__panel h2 {
                margin-bottom: 12px;
                font-size: clamp(1.5rem, 2.4vw, var(--block-heading-max));
            }

            .organization-legacy-page .failure__panel p {
                max-width: 78ch;
                color: var(--muted);
                line-height: 1.75;
            }

            .organization-legacy-page .bridge__list article {
                padding: 16px 18px;
                border-radius: var(--radius-md);
                background: rgba(255, 255, 255, 0.08);
                border: 1px solid rgba(255, 255, 255, 0.08);
            }

            .organization-legacy-page .bridge__list strong {
                display: block;
                margin-bottom: 6px;
                color: #fff7ee;
            }

            .organization-legacy-page .closing__panel {
                padding: 34px;
                display: grid;
                grid-template-columns: minmax(0, 0.9fr) minmax(320px, 1.1fr);
                gap: 24px;
                align-items: start;
            }

            .organization-legacy-page .closing__panel h2 {
                margin-bottom: 10px;
                font-size: clamp(1.55rem, 2.6vw, var(--block-heading-max));
            }

            .organization-legacy-page .contact-form {
                padding: 24px;
                border: 1px solid rgba(255, 255, 255, 0.58);
                box-shadow: var(--shadow);
            }

            .organization-legacy-page .form-status {
                margin-bottom: 16px;
                padding: 14px 16px;
                border-radius: var(--radius-md);
                background: rgba(30, 71, 61, 0.1);
                color: var(--forest-deep);
                font-weight: 600;
            }

            .organization-legacy-page .contact-form form {
                display: grid;
                gap: 16px;
            }

            .organization-legacy-page .contact-form label,
            .organization-legacy-page .contact-form .checkbox-field {
                display: grid;
                gap: 8px;
            }

            .organization-legacy-page .contact-form label span,
            .organization-legacy-page .checkbox-field__text {
                font-size: 0.96rem;
                font-weight: 600;
                color: var(--ink);
            }

            .organization-legacy-page .contact-form input,
            .organization-legacy-page .contact-form textarea {
                width: 100%;
                border: 1px solid rgba(23, 35, 33, 0.14);
                border-radius: 16px;
                padding: 13px 15px;
                background: rgba(255, 255, 255, 0.82);
                color: var(--ink);
                font: inherit;
            }

            .organization-legacy-page .contact-form textarea {
                min-height: 180px;
                resize: vertical;
            }

            .organization-legacy-page .checkbox-field {
                align-items: start;
            }

            .organization-legacy-page .checkbox-field label {
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }

            .organization-legacy-page .checkbox-field__note {
                margin: 0;
                color: var(--muted);
                font-family: Arial, Helvetica, sans-serif;
                font-size: 0.94rem;
                line-height: 1.7;
            }

            .organization-legacy-page .checkbox-field__note a {
                color: var(--clay-deep);
                font-weight: 700;
            }

            .organization-legacy-page .checkbox-field input[type="checkbox"] {
                width: 18px;
                height: 18px;
                margin-top: 2px;
                padding: 0;
            }

            .organization-legacy-page .field-error {
                color: #9f2f1a;
                font-size: 0.9rem;
            }

            @media (max-width: 1040px) {
                .organization-legacy-page .hero,
                .organization-legacy-page .bridge__panel,
                .organization-legacy-page .offer-grid,
                .organization-legacy-page .problem__grid,
                .organization-legacy-page .plan__grid,
                .organization-legacy-page .closing__panel,
                .organization-legacy-page .failure__panel {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 720px) {
                .organization-legacy-page {
                    gap: 32px;
                }

                .organization-legacy-page section + section {
                    padding-top: 0;
                }

                .organization-legacy-page .hero__panel,
                .organization-legacy-page .hero__sidebar,
                .organization-legacy-page .offer-card,
                .organization-legacy-page .plan__step,
                .organization-legacy-page .bridge__panel,
                .organization-legacy-page .closing__panel,
                .organization-legacy-page .failure__panel,
                .organization-legacy-page .contact-form,
                .organization-legacy-page .problem__grid article {
                    padding: 20px;
                    border-radius: 24px;
                }

                .organization-legacy-page .hero__panel::after {
                    right: -90px;
                    bottom: -120px;
                    width: 200px;
                    height: 200px;
                }

                .organization-legacy-page .hero__intro,
                .organization-legacy-page .hero__sidebar p,
                .organization-legacy-page .hero__sidebar li,
                .organization-legacy-page .problem__grid p,
                .organization-legacy-page .offer-card p,
                .organization-legacy-page .offer-card li,
                .organization-legacy-page .plan__step p,
                .organization-legacy-page .closing__panel p,
                .organization-legacy-page .failure__panel p,
                .organization-legacy-page .checkbox-field__note {
                    font-size: 0.98rem;
                    line-height: 1.7;
                }

                .organization-legacy-page .hero__proof {
                    grid-template-columns: 1fr;
                    gap: 12px;
                }

                .organization-legacy-page .hero__proof article {
                    padding: 16px;
                }

                .organization-legacy-page .hero__actions,
                .organization-legacy-page .offer-card__footer,
                .organization-legacy-page .nav-actions {
                    display: grid;
                    gap: 12px;
                }

                .organization-legacy-page .hero__actions .pill,
                .organization-legacy-page .offer-card__footer .pill,
                .organization-legacy-page .nav-actions .pill {
                    width: 100%;
                    justify-content: center;
                    text-align: center;
                }

                .organization-legacy-page .offer-card__meta {
                    align-items: flex-start;
                    flex-direction: column;
                    gap: 8px;
                }

                .organization-legacy-page .problem__grid,
                .organization-legacy-page .offer-grid,
                .organization-legacy-page .plan__grid,
                .organization-legacy-page .bridge__list {
                    gap: 16px;
                }

                .organization-legacy-page .problem__grid h3,
                .organization-legacy-page .offer-card h3,
                .organization-legacy-page .plan__step h3,
                .organization-legacy-page .closing__panel h2,
                .organization-legacy-page .hero__sidebar h2 {
                    font-size: clamp(1.2rem, 6vw, 1.6rem);
                    line-height: 1.15;
                }

                .organization-legacy-page .contact-form textarea {
                    min-height: 150px;
                }
            }
        </style>
    </x-slot:head>

    <div class="organization-legacy-page">
        <section class="hero">
            <div class="hero__panel">
                <span class="eyebrow">{{ __('hermes.organizations_page.eyebrow') }}</span>
                <h1>{{ __('hermes.home.hero_title') }}</h1>
                <p class="hero__intro">{{ __('hermes.home.hero_intro') }}</p>

                <div class="hero__actions">
                    <a class="pill pill--strong" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">
                        {{ __('hermes.home.hero_primary') }}
                    </a>
                    <a class="pill" href="#contact">{{ __('hermes.home.hero_secondary') }}</a>
                </div>

                <div class="hero__proof">
                    <article>
                        <strong>{{ __('hermes.home.proof_quick_scans_title') }}</strong>
                        <span>{{ __('hermes.home.proof_quick_scans_text') }}</span>
                    </article>
                    <article>
                        <strong>{{ __('hermes.home.proof_plan_title') }}</strong>
                        <span>{{ __('hermes.home.proof_plan_text') }}</span>
                    </article>
                    <article>
                        <strong>{{ __('hermes.home.proof_insight_title') }}</strong>
                        <span>{{ __('hermes.home.proof_insight_text') }}</span>
                    </article>
                </div>
            </div>

            <aside class="hero__sidebar">
                <span class="eyebrow eyebrow--light">{{ __('hermes.home.sidebar_eyebrow') }}</span>
                <h2>{{ __('hermes.home.sidebar_title') }}</h2>
                <p>{{ __('hermes.home.sidebar_text') }}</p>

                <ul>
                    <li>{{ __('hermes.home.sidebar_point_1') }}</li>
                    <li>{{ __('hermes.home.sidebar_point_2') }}</li>
                    <li>{{ __('hermes.home.sidebar_point_3') }}</li>
                </ul>

                <div class="sidebar-box">{!! __('hermes.home.sidebar_box') !!}</div>
            </aside>
        </section>

        <section class="problem">
            <x-hermes-section-header
                :tagline="__('hermes.home.why_tagline')"
                :heading="__('hermes.home.why_heading')"
            />

            <div class="problem__grid">
                <article>
                    <h3>{{ __('hermes.home.why_external_title') }}</h3>
                    <p>{{ __('hermes.home.why_external_text') }}</p>
                </article>

                <article>
                    <h3>{{ __('hermes.home.why_internal_title') }}</h3>
                    <p>{{ __('hermes.home.why_internal_text') }}</p>
                </article>

                <article>
                    <h3>{{ __('hermes.home.why_philosophical_title') }}</h3>
                    <p>{{ __('hermes.home.why_philosophical_text') }}</p>
                </article>
            </div>
        </section>

        <section class="failure">
            <div class="failure__panel">
                <div class="tagline">{{ __('hermes.home.failure_tagline') }}</div>
                <h2>{{ __('hermes.home.failure_title') }}</h2>
                <p>{{ __('hermes.home.failure_text') }}</p>
                <p>{{ __('hermes.home.failure_note') }}</p>
            </div>
        </section>

        <section class="offers" id="diensten">
            <x-hermes-section-header
                :tagline="__('hermes.home.offers_tagline')"
                :heading="__('hermes.home.offers_heading')"
            />

            <div class="offer-grid">
                <article class="offer-card offer-card--featured">
                    <div class="offer-card__meta">
                        <span class="offer-badge">{{ __('hermes.home.available_now') }}</span>
                        <span>{{ __('hermes.home.login_required') }}</span>
                    </div>

                    <div>
                        <h3>{{ __('hermes.home.offer_1_title') }}</h3>
                        <p>{{ __('hermes.home.offer_1_text') }}</p>
                    </div>

                    <ul>
                        <li>{{ __('hermes.home.offer_1_point_1') }}</li>
                        <li>{{ __('hermes.home.offer_1_point_2') }}</li>
                        <li>{{ __('hermes.home.offer_1_point_3') }}</li>
                    </ul>

                    <div class="offer-card__footer">
                        <span class="offer-note">{{ __('hermes.home.offer_1_note') }}</span>
                    </div>
                </article>

                <article class="offer-card">
                    <div class="offer-card__meta">
                        <span class="offer-badge">{{ __('hermes.home.consultancy') }}</span>
                        <span>{{ __('hermes.home.from_diagnosis_to_execution') }}</span>
                    </div>

                    <div>
                        <h3>{{ __('hermes.home.offer_3_title') }}</h3>
                        <p>{{ __('hermes.home.offer_3_text') }}</p>
                    </div>

                    <ul>
                        <li>{{ __('hermes.home.offer_3_point_1') }}</li>
                        <li>{{ __('hermes.home.offer_3_point_2') }}</li>
                        <li>{{ __('hermes.home.offer_3_point_3') }}</li>
                    </ul>

                    <div class="offer-card__footer">
                        <span class="offer-note">{{ __('hermes.home.offer_3_note') }}</span>
                    </div>
                </article>
            </div>
        </section>

        <section class="plan">
            <x-hermes-section-header
                :tagline="__('hermes.home.plan_tagline')"
                :heading="__('hermes.home.plan_heading')"
            />

            <div class="plan__grid">
                <article class="plan__step">
                    <div class="plan__number">1</div>
                    <h3>{{ __('hermes.home.plan_1_title') }}</h3>
                    <p>{{ __('hermes.home.plan_1_text') }}</p>
                </article>

                <article class="plan__step">
                    <div class="plan__number">2</div>
                    <h3>{{ __('hermes.home.plan_2_title') }}</h3>
                    <p>{{ __('hermes.home.plan_2_text') }}</p>
                </article>

                <article class="plan__step">
                    <div class="plan__number">3</div>
                    <h3>{{ __('hermes.home.plan_3_title') }}</h3>
                    <p>{{ __('hermes.home.plan_3_text') }}</p>
                </article>
            </div>
        </section>

        <section class="bridge">
            <div class="bridge__panel">
                <div>
                    <span class="eyebrow eyebrow--light">{{ __('hermes.home.bridge_eyebrow') }}</span>
                    <h2>{{ __('hermes.home.bridge_title') }}</h2>
                    <p>{{ __('hermes.home.bridge_text') }}</p>

                    <div class="bridge__highlight">{{ __('hermes.home.bridge_highlight') }}</div>
                </div>

                <div class="bridge__list">
                    <article>
                        <strong>{{ __('hermes.home.bridge_point_2_title') }}</strong>
                        <p>{{ __('hermes.home.bridge_point_2_text') }}</p>
                    </article>

                    <article>
                        <strong>{{ __('hermes.home.bridge_point_1_title') }}</strong>
                        <p>{{ __('hermes.home.bridge_point_1_text') }}</p>
                    </article>

                    <article>
                        <strong>{{ __('hermes.home.bridge_point_3_title') }}</strong>
                        <p>{{ __('hermes.home.bridge_point_3_text') }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="closing" id="contact">
            <div class="closing__panel">
                <div>
                    <div class="tagline">{{ __('hermes.home.closing_tagline') }}</div>
                    <h2>{{ __('hermes.home.closing_title') }}</h2>
                    <p>{{ __('hermes.home.closing_text') }}</p>

                    <div class="nav-actions">
                        <a class="pill pill--strong" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">
                            {{ __('hermes.home.hero_primary') }}
                        </a>
                    </div>
                </div>

                <div class="contact-form">
                    @if (session('status'))
                        <div class="form-status">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <label for="contact-name">
                            <span>{{ __('hermes.home.contact_name') }}</span>
                            <input
                                id="contact-name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                autocomplete="name"
                                required
                            >
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <label for="contact-email">
                            <span>{{ __('hermes.home.contact_email') }}</span>
                            <input
                                id="contact-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                            >
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <label for="contact-message">
                            <span>{{ __('hermes.home.contact_message') }}</span>
                            <textarea id="contact-message" name="message" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </label>

                        <div class="checkbox-field">
                            <label for="contact-consent">
                                <input
                                    id="contact-consent"
                                    type="checkbox"
                                    name="privacy_consent"
                                    value="1"
                                    @checked(old('privacy_consent'))
                                    required
                                >
                                <span class="checkbox-field__text">{{ __('hermes.home.contact_consent') }}</span>
                            </label>
                            <p class="checkbox-field__note">
                                {!! __('hermes.home.contact_privacy_notice', ['url' => route('privacy.show')]) !!}
                            </p>
                            @error('privacy_consent')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="nav-actions">
                            <button type="submit" class="pill pill--strong">{{ __('hermes.home.contact_submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-layouts.hermes-public>
