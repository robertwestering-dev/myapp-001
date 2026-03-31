<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('hermes.academy.title') }}</title>
    <x-favicon-links />
    <style>
        :root {
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --forest: #20453a;
            --forest-soft: #2f5f52;
            --paper: rgba(255, 255, 255, 0.82);
            --shadow: 0 24px 60px rgba(24, 34, 30, 0.14);
            --adaptability: #c46836;
            --resilience: #2a6a6d;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Georgia", "Times New Roman", serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(217, 106, 43, 0.18), transparent 32%),
                radial-gradient(circle at 85% 20%, rgba(32, 69, 58, 0.14), transparent 28%),
                linear-gradient(180deg, #f8f2e8 0%, #f2ece2 48%, #ebe3d8 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .topbar__inner,
        .academy-page,
        .site-footer__inner {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(244, 237, 227, 0.78);
            border-bottom: 1px solid rgba(23, 35, 33, 0.08);
        }

        .topbar__inner {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar__left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
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
            color: var(--accent-deep);
        }

        .topbar__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .locale-switcher__select,
        .pill {
            font-family: Arial, Helvetica, sans-serif;
        }

        .locale-switcher__select {
            min-width: 72px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.82);
            color: var(--ink);
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
            padding: 12px 22px;
            border-radius: 999px;
            border: 1px solid transparent;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .pill--neutral {
            background: linear-gradient(135deg, #8a8f97 0%, #666c74 100%);
        }

        .academy-page {
            flex: 1;
            padding: 48px 0 64px;
            display: grid;
            gap: 24px;
        }

        .academy-hero,
        .academy-grid {
            padding: 34px;
            border-radius: 32px;
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .academy-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(240px, 0.8fr);
            gap: 24px;
        }

        .academy-hero__eyebrow,
        .academy-card__eyebrow {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--forest-soft);
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        h1 {
            font-size: clamp(1.8rem, 3.4vw, 3rem);
            line-height: 1.05;
            margin-top: 10px;
        }

        .academy-hero p,
        .academy-card p,
        .academy-card li,
        .academy-card__meta dd {
            color: var(--muted);
        }

        .academy-hero__stats {
            display: grid;
            gap: 16px;
            align-content: start;
        }

        .academy-hero__stat {
            padding: 18px;
            border-radius: 22px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }

        .academy-hero__stat strong {
            display: block;
            font-size: 2rem;
            margin-top: 8px;
        }

        .academy-grid {
            display: grid;
            gap: 20px;
        }

        .academy-grid__cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .academy-card {
            display: grid;
            gap: 18px;
            padding: 24px;
            border-radius: 28px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.62);
        }

        .academy-card--adaptability {
            box-shadow: inset 0 4px 0 var(--adaptability);
        }

        .academy-card--resilience {
            box-shadow: inset 0 4px 0 var(--resilience);
        }

        .academy-card__title-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
        }

        .academy-card__badge {
            padding: 8px 12px;
            border-radius: 999px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }

        .academy-card__meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .academy-card__meta div {
            display: grid;
            gap: 6px;
        }

        .academy-card__meta dt {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--forest-soft);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .academy-card__lists {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .academy-card ul {
            margin: 0;
            padding-left: 18px;
            display: grid;
            gap: 8px;
        }

        .academy-card__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .site-footer {
            background: rgba(244, 239, 230, 0.78);
            border-top: 1px solid rgba(22, 33, 29, 0.08);
        }

        .site-footer__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            color: var(--muted);
        }

        @media (max-width: 920px) {
            .academy-hero,
            .academy-grid__cards,
            .academy-card__lists,
            .academy-card__meta {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .topbar__inner {
                height: auto;
                min-height: 80px;
                padding: 12px 0;
                align-items: flex-start;
                flex-direction: column;
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
                flex-wrap: wrap;
            }

            .academy-hero,
            .academy-grid {
                padding: 26px;
            }

            .academy-card__title-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header :href="route('dashboard')" :showBooking="false">
        <x-slot:menu>
            <a href="{{ route('dashboard') }}">{{ __('hermes.academy.back_to_dashboard') }}</a>
        </x-slot:menu>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pill pill--neutral">{{ __('hermes.dashboard.logout') }}</button>
        </form>
    </x-hermes-header>

    <main class="academy-page">
        <section class="academy-hero">
            <div>
                <span class="academy-hero__eyebrow">{{ __('hermes.academy.eyebrow') }}</span>
                <h1>{{ __('hermes.academy.heading') }}</h1>
                <p>{{ __('hermes.academy.intro') }}</p>
            </div>

            <div class="academy-hero__stats">
                <div class="academy-hero__stat">
                    <span>{{ __('hermes.academy.course_count_label') }}</span>
                    <strong>{{ $courses->count() }}</strong>
                </div>
                <div class="academy-hero__stat">
                    <span>{{ __('hermes.academy.access_label') }}</span>
                    <strong>{{ $user->email }}</strong>
                </div>
            </div>
        </section>

        <section class="academy-grid" aria-labelledby="academy-catalog-title">
            <div>
                <span class="academy-hero__eyebrow">{{ __('hermes.academy.catalog_eyebrow') }}</span>
                <h2 id="academy-catalog-title">{{ __('hermes.academy.catalog_title') }}</h2>
            </div>

            <div class="academy-grid__cards">
                @forelse ($courses as $course)
                    <article class="academy-card academy-card--{{ $course->theme }}">
                        <div class="academy-card__title-row">
                            <div>
                                <span class="academy-card__eyebrow">{{ __('hermes.academy.course_label') }}</span>
                                <h3>{{ $course->titleForLocale() }}</h3>
                            </div>

                            <span class="academy-card__badge">
                                {{ $course->isAvailable() ? __('hermes.academy.status_available') : __('hermes.academy.status_pending') }}
                            </span>
                        </div>

                        <p>{{ $course->summaryForLocale() }}</p>

                        <dl class="academy-card__meta">
                            <div>
                                <dt>{{ __('hermes.academy.audience') }}</dt>
                                <dd>{{ $course->audienceForLocale() }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('hermes.academy.duration') }}</dt>
                                <dd>{{ __('hermes.academy.minutes', ['count' => $course->estimated_minutes]) }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('hermes.academy.goal') }}</dt>
                                <dd>{{ $course->goalForLocale() }}</dd>
                            </div>
                            <div>
                                <dt>{{ __('hermes.academy.format') }}</dt>
                                <dd>{{ __('hermes.academy.web_export_format') }}</dd>
                            </div>
                        </dl>

                        <div class="academy-card__lists">
                            <div>
                                <strong>{{ __('hermes.academy.learning_goals') }}</strong>
                                <ul>
                                    @foreach ($course->learningGoalsForLocale() as $learningGoal)
                                        <li>{{ $learningGoal }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <div>
                                <strong>{{ __('hermes.academy.contents') }}</strong>
                                <ul>
                                    @foreach ($course->contentsForLocale() as $contentItem)
                                        <li>{{ $contentItem }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="academy-card__actions">
                            @if ($course->launchUrl())
                                <a href="{{ $course->launchUrl() }}" class="pill" target="_blank" rel="noopener noreferrer">
                                    {{ __('hermes.academy.open_course') }}
                                </a>
                            @else
                                <span>{{ __('hermes.academy.pending_copy') }}</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <article class="academy-card">
                        <strong>{{ __('hermes.academy.empty_title') }}</strong>
                        <p>{{ __('hermes.academy.empty_text') }}</p>
                    </article>
                @endforelse
            </div>
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
