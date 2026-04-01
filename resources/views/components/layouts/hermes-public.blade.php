@props([
    'title',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
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
    {{ $head ?? '' }}
</head>
<body>
    <x-hermes-header>
        <x-slot:menu>
            <a href="{{ route('home') }}#diensten">{{ __('hermes.nav.services') }}</a>
            <a href="{{ route('blog.index') }}">{{ __('hermes.nav.blog') }}</a>
            <a href="{{ route('academy.index') }}">{{ __('hermes.nav.academy') }}</a>
            <a href="{{ route('home') }}#contact">{{ __('hermes.nav.contact') }}</a>
        </x-slot:menu>

        <div class="nav-actions">
            @auth
                <a class="pill pill--strong" href="{{ route(auth()->user()->canAccessAdminPortal() ? 'admin.portal' : 'dashboard') }}">
                    {{ auth()->user()->canAccessAdminPortal() ? __('hermes.blog.portal_action') : __('hermes.blog.dashboard_action') }}
                </a>
            @else
                <a class="pill pill--strong" href="{{ route('login') }}">{{ __('hermes.nav.login') }}</a>
            @endauth
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
