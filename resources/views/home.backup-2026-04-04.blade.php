<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $homeTitle = __('hermes.home.title');
        $homeMetaDescription = __('hermes.home.meta_description');
        $homeCanonicalUrl = route('home');
        $homeMetaImage = asset('images/hermes-results-logo.png');
        $homeStructuredData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    'name' => 'Hermes Results',
                    'url' => $homeCanonicalUrl,
                    'description' => $homeMetaDescription,
                    'inLanguage' => app()->getLocale(),
                ],
                [
                    '@type' => 'Organization',
                    'name' => 'Hermes Results',
                    'url' => $homeCanonicalUrl,
                    'logo' => $homeMetaImage,
                    'description' => $homeMetaDescription,
                ],
            ],
        ];
    @endphp
    <title>{{ $homeTitle }}</title>
    <meta name="description" content="{{ $homeMetaDescription }}">
    <link rel="canonical" href="{{ $homeCanonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $homeTitle }}">
    <meta property="og:description" content="{{ $homeMetaDescription }}">
    <meta property="og:url" content="{{ $homeCanonicalUrl }}">
    <meta property="og:image" content="{{ $homeMetaImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $homeTitle }}">
    <meta name="twitter:description" content="{{ $homeMetaDescription }}">
    <meta name="twitter:image" content="{{ $homeMetaImage }}">
    <x-favicon-links />
    <script type="application/ld+json">{!! json_encode($homeStructuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
    <style>
        :root {
            --bg: #f4ede3;
            --bg-deep: #efe5d7;
            --panel: rgba(255, 250, 244, 0.82);
            --panel-strong: rgba(255, 255, 255, 0.92);
            --ink: #172321;
            --muted: #56655f;
            --line: rgba(23, 35, 33, 0.1);
            --forest: #1e473d;
            --forest-deep: #102a23;
            --clay: #bc5b2c;
            --clay-deep: #8d3f18;
            --gold: #d6b37a;
            --shadow: 0 28px 70px rgba(16, 33, 28, 0.14);
            --radius-xl: 34px;
            --radius-lg: 24px;
            --radius-md: 18px;
            --content: 1180px;
            --block-heading-max: 2.45rem;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: var(--ink);
            font-family: "Avenir Next", "Segoe UI", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(188, 91, 44, 0.2), transparent 34%),
                radial-gradient(circle at 88% 12%, rgba(30, 71, 61, 0.15), transparent 30%),
                linear-gradient(180deg, var(--bg) 0%, var(--bg-deep) 52%, #ebdfcf 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(244, 237, 227, 0.78);
            border-bottom: 1px solid rgba(23, 35, 33, 0.08);
        }

        .topbar__inner,
        .site-footer__inner,
        .hero,
        .problem,
        .offers,
        .plan,
        .bridge,
        .closing {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar__left,
        .topbar__actions,
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .topbar__left {
            min-width: 0;
            flex: 1;
        }

        .topbar__menu {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-left: 16px;
            white-space: nowrap;
        }

        .topbar__menu a {
            font-size: 0.98rem;
            font-weight: 600;
            color: var(--ink);
        }

        .topbar__menu a:hover {
            color: var(--clay-deep);
        }

        .locale-switcher {
            display: inline-flex;
            align-items: center;
            font-family: Arial, Helvetica, sans-serif;
        }

        .locale-switcher__label {
            font-size: 0.9rem;
            color: var(--muted);
        }

        .locale-switcher__select {
            min-width: 72px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.82);
            color: var(--ink);
            font: inherit;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 54px;
            max-width: 100%;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.72);
            color: var(--ink);
            font-size: 0.94rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .pill--strong {
            color: #fff;
            border-color: transparent;
            background: linear-gradient(135deg, var(--clay) 0%, var(--clay-deep) 100%);
            box-shadow: 0 12px 28px rgba(141, 63, 24, 0.28);
        }

        .pill--ghost {
            background: transparent;
        }

        .pill--booking {
            border-color: transparent;
            color: #f8f3eb;
            background: linear-gradient(180deg, rgba(30, 71, 61, 0.96), rgba(16, 42, 35, 0.98));
        }

        main {
            flex: 1;
            padding: 34px 0 64px;
        }

        .site-footer {
            background: rgba(244, 237, 227, 0.78);
            border-top: 1px solid rgba(23, 35, 33, 0.08);
        }

        .site-footer__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(320px, 0.82fr);
            gap: 24px;
            align-items: stretch;
        }

        .hero__panel,
        .hero__sidebar,
        .problem__grid article,
        .offer-card,
        .plan__step,
        .bridge__panel,
        .closing__panel {
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
        }

        .hero__panel,
        .hero__sidebar,
        .bridge__panel,
        .closing__panel {
            overflow: hidden;
            position: relative;
        }

        .hero__panel {
            padding: 42px;
            border-radius: var(--radius-xl);
            background:
                radial-gradient(circle at 82% 24%, rgba(214, 179, 122, 0.28), transparent 24%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(250, 243, 234, 0.82));
        }

        .hero__panel::after {
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

        .hero__sidebar,
        .bridge__panel {
            padding: 30px;
            border-radius: var(--radius-xl);
            background:
                linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                var(--forest);
            color: #f8f1e7;
        }

        .eyebrow {
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

        .eyebrow--light {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(248, 241, 231, 0.86);
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1,
        h2,
        h3 {
            font-family: "Georgia", "Times New Roman", serif;
            letter-spacing: -0.02em;
        }

        h1 {
            margin: 20px 0 18px;
            font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
            line-height: 0.96;
            max-width: none;
        }

        .hero__intro {
            max-width: 58ch;
            font-size: 1.08rem;
            line-height: 1.8;
            color: var(--muted);
            margin-bottom: 26px;
        }

        .hero__actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 28px;
        }

        .hero__proof {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .hero__proof article {
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.62);
            border: 1px solid rgba(23, 35, 33, 0.08);
        }

        .hero__proof strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1.02rem;
        }

        .hero__proof span,
        .offer-card p,
        .offer-card li,
        .plan__step p,
        .closing__panel p,
        .problem__grid p {
            color: var(--muted);
            line-height: 1.7;
        }

        .hero__sidebar h2,
        .bridge__panel h2 {
            font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
            line-height: 1.05;
            margin: 14px 0 18px;
            color: #fff7ee;
        }

        .hero__sidebar p,
        .bridge__panel p,
        .hero__sidebar li {
            color: rgba(248, 241, 231, 0.84);
            line-height: 1.8;
        }

        .hero__sidebar ul,
        .offer-card ul {
            margin: 0;
            padding-left: 18px;
        }

        .hero__sidebar .sidebar-box,
        .bridge__highlight {
            margin-top: 22px;
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        section {
            margin-top: 48px;
        }

        section + section {
            padding-top: 20px;
        }

        .section-head {
            display: block;
            width: 100%;
            margin-bottom: 18px;
        }

        .section-head h2 {
            margin-bottom: 0;
            font-size: clamp(1.5rem, 2.4vw, var(--block-heading-max));
        }

        .tagline {
            margin-bottom: 10px;
            color: var(--clay-deep);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .problem__grid,
        .offer-grid,
        .plan__grid {
            display: grid;
            gap: 18px;
        }

        .problem__grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .problem__grid article,
        .offer-card,
        .plan__step,
        .closing__panel,
        .contact-form {
            border-radius: var(--radius-lg);
            background: var(--panel);
        }

        .problem__grid article {
            padding: 24px;
        }

        .problem__grid h3,
        .offer-card h3,
        .plan__step h3 {
            margin-bottom: 10px;
            font-size: 1.35rem;
        }

        .offer-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .offer-card {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .offer-card--featured {
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 236, 222, 0.94));
            transform: translateY(-4px);
        }

        .offer-card__meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 0.88rem;
            color: var(--muted);
        }

        .offer-badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(188, 91, 44, 0.12);
            color: var(--clay-deep);
            font-weight: 700;
        }

        .offer-card__footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .offer-note {
            font-size: 0.92rem;
            color: var(--muted);
        }

        .plan__grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .plan__step {
            padding: 24px;
        }

        .plan__number {
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

        .bridge__panel {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(280px, 0.9fr);
            gap: 24px;
            align-items: center;
        }

        .bridge__list {
            display: grid;
            gap: 12px;
        }

        .bridge__list article {
            padding: 16px 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .bridge__list strong {
            display: block;
            margin-bottom: 6px;
            color: #fff7ee;
        }

        .closing__panel {
            padding: 34px;
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(320px, 1.1fr);
            gap: 24px;
            align-items: start;
        }

        .closing__panel h2 {
            margin-bottom: 10px;
            font-size: clamp(1.55rem, 2.6vw, var(--block-heading-max));
        }

        .contact-form {
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
        }

        .form-status {
            margin-bottom: 16px;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            background: rgba(30, 71, 61, 0.1);
            color: var(--forest-deep);
            font-weight: 600;
        }

        .contact-form form {
            display: grid;
            gap: 16px;
        }

        .contact-form label,
        .contact-form .checkbox-field {
            display: grid;
            gap: 8px;
        }

        .contact-form label span,
        .checkbox-field__text {
            font-size: 0.96rem;
            font-weight: 600;
            color: var(--ink);
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            border: 1px solid rgba(23, 35, 33, 0.14);
            border-radius: 16px;
            padding: 13px 15px;
            background: rgba(255, 255, 255, 0.82);
            color: var(--ink);
            font: inherit;
        }

        .contact-form textarea {
            min-height: 180px;
            resize: vertical;
        }

        .checkbox-field {
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

        .field-error {
            color: #9f2f1a;
            font-size: 0.9rem;
        }

        @media (max-width: 1040px) {
            .hero,
            .bridge__panel,
            .offer-grid,
            .problem__grid,
            .plan__grid,
            .closing__panel {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 780px) {
            .topbar__inner,
            .section-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar__inner {
                height: auto;
                min-height: 80px;
                padding: 12px 0;
            }

            .topbar__left {
                width: 100%;
                flex-wrap: wrap;
                gap: 10px 14px;
            }

            .topbar__menu {
                margin-left: 0;
            }

            .topbar__actions {
                width: 100%;
                justify-content: flex-end;
            }

            .hero__panel,
            .hero__sidebar,
            .problem__grid article,
            .offer-card,
            .plan__step,
            .bridge__panel,
            .closing__panel {
                padding: 22px;
            }

            .hero__proof {
                grid-template-columns: 1fr;
            }

            h1 {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <x-slot:menu>
            <a href="#diensten">{{ __('hermes.nav.services') }}</a>
            <a href="{{ route('blog.index') }}">{{ __('hermes.nav.blog') }}</a>
            <a href="{{ route('academy.index') }}">{{ __('hermes.nav.academy') }}</a>
            <a href="#contact">{{ __('hermes.nav.contact') }}</a>
        </x-slot:menu>

        <div class="nav-actions">
            <a class="pill pill--strong" href="{{ route('login') }}">{{ __('hermes.nav.login') }}</a>
        </div>
    </x-hermes-header>

    <main>
        <section class="hero">
            <div class="hero__panel">
                <span class="eyebrow">{{ __('hermes.home.eyebrow') }}</span>
                <h1>{{ __('hermes.home.hero_title') }}</h1>
                <p class="hero__intro">{{ __('hermes.home.hero_intro') }}</p>

                <div class="hero__actions">
                    <a class="pill pill--strong" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">
                        {{ __('hermes.home.hero_primary') }}
                    </a>
                    <a class="pill" href="#diensten">{{ __('hermes.home.hero_secondary') }}</a>
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
                        <a class="pill pill--strong" href="\login" target="_blank" rel="noopener noreferrer">{{ __('hermes.home.offer_1_action') }}</a>
                    </div>
                </article>

                <article class="offer-card">
                    <div class="offer-card__meta">
                        <span class="offer-badge">{{ __('hermes.home.available_now') }}</span>
                        <span>{{ __('hermes.home.login_required') }}</span>
                    </div>

                    <div>
                        <h3>{{ __('hermes.home.offer_2_title') }}</h3>
                        <p>{{ __('hermes.home.offer_2_text') }}</p>
                    </div>

                    <ul>
                        <li>{{ __('hermes.home.offer_2_point_1') }}</li>
                        <li>{{ __('hermes.home.offer_2_point_2') }}</li>
                        <li>{{ __('hermes.home.offer_2_point_3') }}</li>
                    </ul>

                    <div class="offer-card__footer">
                        <span class="offer-note">{{ __('hermes.home.offer_2_note') }}</span>
                        <a class="pill pill--strong" href="\login" target="_blank" rel="noopener noreferrer">{{ __('hermes.home.offer_2_action') }}</a>
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
                        <a class="pill" href="https://calendly.com/robertwestering/30min" target="_blank" rel="noopener noreferrer">{{ __('hermes.home.offer_3_action') }}</a>
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
                        <strong>{{ __('hermes.home.bridge_point_1_title') }}</strong>
                        <p>{{ __('hermes.home.bridge_point_1_text') }}</p>
                    </article>

                    <article>
                        <strong>{{ __('hermes.home.bridge_point_2_title') }}</strong>
                        <p>{{ __('hermes.home.bridge_point_2_text') }}</p>
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
                            <textarea
                                id="contact-message"
                                name="message"
                                required
                            >{{ old('message') }}</textarea>
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
    </main>

    <x-hermes-footer />
</body>
</html>
