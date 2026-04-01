<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('hermes.dashboard.title') }}</title>
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
        .site-footer__inner,
        .welcome {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        .topbar__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
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

        .brand {
            display: flex;
            align-items: center;
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 60px;
            max-width: 100%;
            border-radius: 12px;
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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .pill--neutral {
            background: linear-gradient(135deg, #8a8f97 0%, #666c74 100%);
            color: #fff;
        }

        .pill--booking {
            background: linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97));
            color: #fff;
            border-color: transparent;
        }

        .welcome {
            flex: 1;
            padding: 48px 0 64px;
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

        .site-footer {
            background: rgba(244, 239, 230, 0.78);
            border-top: 1px solid rgba(22, 33, 29, 0.08);
        }

        .welcome__card {
            padding: 40px;
            border-radius: 32px;
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(1.3rem, 2.5vw, 2.4rem);
            line-height: 1.08;
        }

        p {
            margin: 0;
            color: var(--muted);
            font-size: 1.1rem;
        }

        .questionnaire-list {
            display: grid;
            gap: 18px;
            margin-top: 28px;
        }

        .dashboard-stack {
            display: grid;
            gap: 28px;
            margin-top: 28px;
        }

        .dashboard-section {
            display: grid;
            gap: 18px;
        }

        .dashboard-section__heading {
            display: grid;
            gap: 8px;
        }

        .dashboard-section__eyebrow {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--forest-soft);
        }

        .academy-spotlight {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(220px, 0.8fr);
            gap: 22px;
            padding: 26px;
            border-radius: 28px;
            background:
                linear-gradient(145deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
            color: #f6efe5;
            box-shadow: var(--shadow);
        }

        .academy-spotlight p {
            color: rgba(246, 239, 229, 0.82);
        }

        .academy-spotlight__content,
        .academy-spotlight__meta {
            display: grid;
            gap: 14px;
        }

        .academy-spotlight__meta {
            align-content: start;
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .academy-spotlight__stat {
            display: grid;
            gap: 6px;
        }

        .academy-spotlight__stat strong {
            font-size: 2rem;
            line-height: 1;
        }

        .questionnaire-card {
            display: grid;
            gap: 10px;
            padding: 22px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.58);
            border: 1px solid var(--line);
        }

        .questionnaire-card__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        @media (max-width: 720px) {
            .brand__logo {
                height: 60px;
            }

            .welcome__card {
                padding: 28px;
            }

            .academy-spotlight {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pill pill--neutral">{{ __('hermes.dashboard.logout') }}</button>
        </form>
    </x-hermes-header>

    <main class="welcome">
        <section class="welcome__card">
            <h1>{{ __('hermes.dashboard.welcome', ['email' => auth()->user()->email]) }}</h1>
            <p>{{ __('hermes.dashboard.logged_in') }}</p>

            <div class="dashboard-stack">
                <section class="dashboard-section" aria-labelledby="academy-section-title">
                    <div class="dashboard-section__heading">
                        <span class="dashboard-section__eyebrow">{{ __('hermes.dashboard.academy_eyebrow') }}</span>
                        <h2 id="academy-section-title">{{ __('hermes.dashboard.academy_title') }}</h2>
                        <p>{{ __('hermes.dashboard.academy_text') }}</p>
                    </div>

                    <article class="academy-spotlight">
                        <div class="academy-spotlight__content">
                            <strong>{{ __('hermes.dashboard.academy_card_title') }}</strong>
                            <p>{{ __('hermes.dashboard.academy_card_text') }}</p>
                            <div>
                                <a href="{{ route('academy.index') }}" class="pill">{{ __('hermes.dashboard.academy_action') }}</a>
                            </div>
                        </div>

                        <div class="academy-spotlight__meta">
                            <div class="academy-spotlight__stat">
                                <span>{{ __('hermes.dashboard.academy_courses_label') }}</span>
                                <strong>{{ $academyCourseCount }}</strong>
                            </div>
                            <span>{{ __('hermes.dashboard.academy_meta') }}</span>
                        </div>
                    </article>
                </section>

                <section class="dashboard-section" aria-labelledby="questionnaires-section-title">
                    <div class="dashboard-section__heading">
                        <span class="dashboard-section__eyebrow">{{ __('hermes.dashboard.questionnaires_eyebrow') }}</span>
                        <h2 id="questionnaires-section-title">{{ __('hermes.dashboard.questionnaires_title') }}</h2>
                    </div>

                    <div class="questionnaire-list">
                        @forelse ($availableQuestionnaires as $availableQuestionnaire)
                            <article class="questionnaire-card">
                                <div>
                                    <strong>{{ $availableQuestionnaire->questionnaire->title }}</strong>
                                </div>
                                <div>{{ $availableQuestionnaire->questionnaire->description ?: __('hermes.dashboard.description_fallback') }}</div>
                                <div class="questionnaire-card__actions">
                                    <a href="{{ route('questionnaire-responses.show', $availableQuestionnaire) }}" class="pill">{{ __('hermes.dashboard.open_questionnaire') }}</a>
                                    @if ($availableQuestionnaire->currentResponse?->submitted_at)
                                        <span>{{ __('hermes.dashboard.last_completed', ['datetime' => $availableQuestionnaire->currentResponse->submitted_at->format('d-m-Y H:i')]) }}</span>
                                    @elseif ($availableQuestionnaire->currentResponse?->last_saved_at)
                                        <span>{{ __('hermes.dashboard.draft_saved', ['datetime' => $availableQuestionnaire->currentResponse->last_saved_at->format('d-m-Y H:i')]) }}</span>
                                    @else
                                        <span>{{ __('hermes.dashboard.not_completed') }}</span>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <article class="questionnaire-card">
                                <strong>{{ __('hermes.dashboard.empty_title') }}</strong>
                                <div>{{ __('hermes.dashboard.empty_text') }}</div>
                            </article>
                        @endforelse
                    </div>
                </section>
            </div>
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
