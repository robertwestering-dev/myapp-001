@props([
    'title',
    'eyebrow',
    'heading',
    'lead',
    'menuActive' => 'portal',
    'showSecondaryMenuItems' => true,
])

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        :root {
            --bg: #f4efe6;
            --paper: rgba(255, 255, 255, 0.78);
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --forest: #20453a;
            --forest-soft: #2f5f52;
            --shadow: 0 24px 60px rgba(24, 34, 30, 0.14);
            --radius-xl: 32px;
            --radius-lg: 24px;
            --radius-md: 18px;
            --content: 1180px;
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

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        .topbar__inner,
        .site-footer__inner,
        .hero,
        .content-section {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
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

        .pill,
        .pill--neutral,
        .ghost-pill,
        .danger-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 11px 18px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            box-shadow: var(--shadow);
            cursor: pointer;
        }

        .pill {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            border-color: transparent;
        }

        .pill--neutral {
            background: linear-gradient(135deg, #8a8f97 0%, #666c74 100%);
            color: #fff;
            border-color: transparent;
        }

        .pill--booking {
            background: linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97));
            color: #fff;
            border-color: transparent;
        }

        .ghost-pill {
            color: var(--ink);
        }

        .danger-pill {
            background: rgba(168, 74, 25, 0.12);
            border-color: rgba(168, 74, 25, 0.2);
            color: var(--accent-deep);
            box-shadow: none;
        }

        main {
            flex: 1;
            padding: 34px 0 60px;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 28px;
            align-items: stretch;
        }

        .hero__panel,
        .hero__side,
        .content-panel,
        .summary-card,
        .notice-card {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .hero__panel,
        .content-panel,
        .notice-card {
            border-radius: var(--radius-xl);
        }

        .hero__panel {
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero__panel::after {
            content: "";
            position: absolute;
            inset: auto -80px -120px auto;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 106, 43, 0.2), transparent 70%);
        }

        .hero__side {
            border-radius: var(--radius-xl);
            padding: 28px;
            background:
                linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                var(--forest);
            color: #f6f2eb;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: var(--forest);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3,
        p {
            margin-top: 0;
        }

        h1 {
            font-size: clamp(1.45rem, 3vw, 2.65rem);
            line-height: 1.08;
            margin: 22px 0 20px;
            max-width: 62ch;
        }

        .lead {
            max-width: 62ch;
            font-size: 1.08rem;
            line-height: 1.7;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .hero__facts {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .fact,
        .summary-card {
            padding: 18px;
            border-radius: var(--radius-md);
            background: rgba(255, 255, 255, 0.52);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }

        .fact strong,
        .summary-card strong {
            display: block;
            font-size: 2rem;
            margin-bottom: 4px;
        }

        .fact span,
        .summary-card span {
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
        }

        .admin-menu {
            display: grid;
            gap: 12px;
        }

        .admin-menu__item {
            display: block;
            padding: 16px 18px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.08);
            color: rgba(246, 242, 235, 0.88);
            font-family: Arial, Helvetica, sans-serif;
        }

        .admin-menu__item--active {
            color: #fff;
            background: linear-gradient(135deg, rgba(217, 106, 43, 0.92), rgba(168, 74, 25, 0.96));
            border-color: transparent;
        }

        .content-section {
            margin-top: 24px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-head h2 {
            font-size: clamp(2rem, 3vw, 3rem);
            margin-bottom: 8px;
        }

        .section-head p {
            max-width: 58ch;
            color: var(--muted);
            line-height: 1.7;
        }

        .tagline {
            font-family: Arial, Helvetica, sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--accent-deep);
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        .content-panel {
            padding: 34px;
        }

        .status,
        .errors {
            margin: 22px 0 0;
            padding: 14px 16px;
            border-radius: 18px;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        .status {
            background: rgba(32, 69, 58, 0.1);
            color: var(--forest);
        }

        .errors {
            background: rgba(168, 74, 25, 0.12);
            color: var(--accent-deep);
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

        @media (max-width: 980px) {
            .hero,
            .hero__facts {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .topbar__inner,
            .section-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero__panel,
            .hero__side,
            .content-panel,
            .notice-card {
                padding: 22px;
            }

            h1 {
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pill pill--neutral">Logout</button>
        </form>
    </x-hermes-header>

    <main>
        <section class="hero">
            <div class="hero__panel">
                <span class="eyebrow">{{ $eyebrow }}</span>
                <h1>{{ $heading }}</h1>
                <p class="lead">{{ $lead }}</p>

                @isset($heroFacts)
                    <div class="hero__facts">
                        {{ $heroFacts }}
                    </div>
                @endisset
            </div>

            <aside class="hero__side">
                <x-admin-menu :active="$menuActive" :show-secondary-items="$showSecondaryMenuItems" />
            </aside>
        </section>

        <section class="content-section">
            {{ $slot }}
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
