@props([
    'title',
    'metaDescription' => null,
    'canonicalUrl' => null,
    'metaImage' => null,
    'ogType' => 'website',
    'structuredData' => null,
    'forceGuestNavigation' => false,
    'showHeaderBooking' => true,
    'showHeaderContactLink' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    @if ($metaDescription)
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    @if ($canonicalUrl)
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
    @endif
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $title }}">
    @if ($metaDescription)
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    @if ($metaImage)
        <meta property="og:image" content="{{ $metaImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    @if ($metaDescription)
        <meta name="twitter:description" content="{{ $metaDescription }}">
    @endif
    @if ($metaImage)
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif
    <x-favicon-links />
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
        .page-shell {
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

        .home-menu-item,
        .home-menu-trigger,
        .home-submenu a {
            font-size: 0.98rem;
            font-weight: 600;
            color: var(--ink);
        }

        .home-menu-item:hover,
        .home-menu-trigger:hover,
        .home-submenu a:hover {
            color: var(--clay-deep);
        }

        .home-menu-dropdown {
            position: relative;
        }

        .home-menu-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .home-submenu {
            position: absolute;
            top: calc(100% + 12px);
            left: 0;
            min-width: 220px;
            padding: 12px;
            border-radius: 22px;
            border: 1px solid rgba(23, 35, 33, 0.1);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 26px 60px rgba(24, 34, 30, 0.16);
            display: grid;
            gap: 4px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: opacity 180ms ease, transform 180ms ease, visibility 180ms ease;
            z-index: 30;
        }

        .home-submenu a {
            padding: 10px 12px;
            border-radius: 14px;
        }

        .home-menu-dropdown:hover .home-submenu,
        .home-menu-dropdown:focus-within .home-submenu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
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

        .pill--booking {
            border-color: transparent;
            color: #f8f3eb;
            background: linear-gradient(180deg, rgba(30, 71, 61, 0.96), rgba(16, 42, 35, 0.98));
        }

        .user-guidance-card {
            display: grid;
            gap: 16px;
            padding: 24px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(23, 35, 33, 0.1);
            box-shadow: var(--shadow);
        }

        .user-guidance-card--accent {
            background:
                linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                var(--forest);
            border-color: rgba(255, 255, 255, 0.08);
            color: #f8f1e7;
        }

        .user-guidance-card__eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--forest);
        }

        .user-guidance-card--accent .user-guidance-card__eyebrow {
            color: rgba(248, 241, 231, 0.86);
        }

        .user-guidance-card--accent .user-guidance-card__body strong {
            color: #f8f1e7;
        }

        .user-guidance-card__body {
            display: grid;
            gap: 10px;
        }

        .user-guidance-card__body strong {
            font-family: "Georgia", "Times New Roman", serif;
            font-size: 1.15rem;
            line-height: 1.2;
        }

        .user-guidance-card__body p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .user-guidance-card--accent .user-guidance-card__body p {
            color: rgba(248, 241, 231, 0.82);
        }

        .user-guidance-card__actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .user-surface-card {
            display: grid;
            gap: 18px;
            padding: 24px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(23, 35, 33, 0.1);
            box-shadow: var(--shadow);
        }

        .user-surface-card--accent {
            background:
                linear-gradient(180deg, rgba(30, 71, 61, 0.98), rgba(16, 42, 35, 0.98)),
                var(--forest);
            border-color: rgba(255, 255, 255, 0.08);
            color: #f8f1e7;
        }

        .user-surface-card--accent .user-page-heading__eyebrow,
        .user-surface-card--accent .user-section-heading__eyebrow,
        .user-surface-card--accent .user-page-heading h1,
        .user-surface-card--accent .user-section-heading h2,
        .user-surface-card--accent .user-page-heading p,
        .user-surface-card--accent .user-section-heading p,
        .user-surface-card--accent .user-page-heading__meta,
        .user-surface-card--accent .user-inline-meta {
            color: #f8f1e7;
        }

        .user-surface-card--accent .user-page-heading__eyebrow,
        .user-surface-card--accent .user-section-heading__eyebrow,
        .user-surface-card--accent .user-page-heading p,
        .user-surface-card--accent .user-section-heading p,
        .user-surface-card--accent .user-page-heading__meta,
        .user-surface-card--accent .user-inline-meta {
            color: rgba(248, 241, 231, 0.82);
        }

        .user-surface-card--accent .user-inline-meta span + span::before {
            color: rgba(248, 241, 231, 0.42);
        }

        .user-surface-card--soft {
            background: rgba(255, 255, 255, 0.58);
        }

        .user-stat-tile {
            display: grid;
            gap: 8px;
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.56);
            border: 1px solid rgba(23, 35, 33, 0.08);
        }

        .user-stat-tile span {
            color: var(--muted);
            font-size: 0.92rem;
            line-height: 1.6;
        }

        .user-stat-tile strong {
            display: block;
            font-family: "Georgia", "Times New Roman", serif;
            font-size: 1.45rem;
            line-height: 1.1;
        }

        .user-surface-card--accent .user-stat-tile {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .user-surface-card--accent .user-stat-tile span {
            color: rgba(248, 241, 231, 0.82);
        }

        .user-page-heading,
        .user-section-heading {
            display: grid;
            gap: 10px;
        }

        .user-page-heading__eyebrow,
        .user-section-heading__eyebrow {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--forest);
        }

        .user-page-heading__body,
        .user-page-heading__meta {
            display: grid;
            gap: 10px;
        }

        .user-page-heading h1,
        .user-section-heading h2 {
            margin: 0;
            font-family: "Georgia", "Times New Roman", serif;
            letter-spacing: -0.02em;
            color: var(--ink);
        }

        .user-page-heading h1 {
            font-size: clamp(1.45rem, 2.5vw, var(--block-heading-max));
            line-height: 0.96;
        }

        .user-section-heading h2 {
            font-size: clamp(1.15rem, 2vw, 1.8rem);
            line-height: 1.08;
        }

        .user-page-heading p,
        .user-section-heading p,
        .user-page-heading__meta {
            margin: 0;
            color: var(--muted);
            line-height: 1.75;
        }

        .user-action-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .user-action-row--center {
            justify-content: center;
        }

        .user-action-row--end {
            justify-content: flex-end;
        }

        .user-inline-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .user-inline-meta span {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .user-inline-meta span + span::before {
            content: '•';
            color: rgba(23, 35, 33, 0.24);
        }

        .user-inline-meta--light {
            color: rgba(248, 241, 231, 0.82);
        }

        .user-inline-meta--light span + span::before {
            color: rgba(248, 241, 231, 0.42);
        }

        .user-filter-panel {
            display: grid;
            gap: 18px;
            padding: 28px;
            border-radius: var(--radius-xl);
            background: var(--panel);
            border: 1px solid rgba(255, 255, 255, 0.58);
            box-shadow: var(--shadow);
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
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.2;
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
        }

        @media (max-width: 780px) {
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
            }
        }
    </style>
    @if ($structuredData)
        <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
    @endif
    {{ $head ?? '' }}
</head>
<body>
    <x-hermes-header :show-booking="$showHeaderBooking" :show-contact-link="$showHeaderContactLink">
        <x-slot:menu>
            @isset($headerMenu)
                {{ $headerMenu }}
            @else
                @if (auth()->check() && ! $forceGuestNavigation)
                    <a href="{{ route('dashboard') }}">{{ __('hermes.dashboard.title') }}</a>
                    <a href="{{ route('questionnaires.index') }}">{{ __('hermes.nav.questionnaires') }}</a>
                    <a href="{{ route('academy.index') }}">{{ __('hermes.nav.academy') }}</a>
                    <a href="{{ route('forum.index') }}">{{ __('hermes.nav.forum') }}</a>
                    <a href="{{ route('blog.index') }}" aria-current="page">{{ __('hermes.nav.blog') }}</a>
                    <a href="{{ route('profile.edit') }}">{{ __('hermes.nav.profile') }}</a>
                @else
                    <a href="{{ route('home') }}#diensten">{{ __('hermes.nav.services') }}</a>
                    <a href="{{ route('blog.index') }}">{{ __('hermes.nav.blog') }}</a>
                    <a href="{{ route('academy.index') }}">{{ __('hermes.nav.academy') }}</a>
                    <a href="{{ route('home') }}#contact">{{ __('hermes.nav.contact') }}</a>
                @endif
            @endisset
        </x-slot:menu>

        <div class="nav-actions">
            @if (auth()->check() && ! $forceGuestNavigation)
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="pill pill--strong">{{ __('hermes.dashboard.logout') }}</button>
                </form>
            @else
                <a class="pill pill--strong" href="{{ route('login') }}">{{ __('hermes.nav.login') }}</a>
            @endif
        </div>
    </x-hermes-header>

    <main>
        <div class="page-shell">
            {{ $slot }}
        </div>
    </main>

    <x-hermes-footer />
</body>
</html>
