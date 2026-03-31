@props([
    'title',
    'backHref',
    'backLabel',
    'eyebrow',
    'heading',
    'lead',
    'formTitle',
    'helper',
    'points' => [],
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

        .topbar__inner,
        .site-footer__inner,
        .page-shell {
            width: min(var(--content), calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
            flex-shrink: 0;
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
        }

        .topbar__inner {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
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
            gap: 10px;
            padding: 12px 22px;
            border-radius: 999px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.68);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            box-shadow: var(--shadow);
        }

        .pill--strong {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-deep) 100%);
            color: #fff;
            border-color: transparent;
            cursor: pointer;
        }

        .pill--booking {
            background: linear-gradient(180deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97));
            color: #fff;
            border-color: transparent;
        }

        .page-shell {
            flex: 1;
            padding: 36px 0 64px;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 28px;
            align-items: stretch;
        }

        .site-footer {
            flex-shrink: 0;
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

        .hero-panel,
        .form-panel {
            background: var(--paper);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .hero-panel {
            border-radius: var(--radius-xl);
            padding: 36px;
            position: relative;
            overflow: hidden;
        }

        .hero-panel::after {
            content: "";
            position: absolute;
            inset: auto -80px -120px auto;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(217, 106, 43, 0.2), transparent 70%);
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
        p {
            margin-top: 0;
        }

        h1 {
            font-size: clamp(1.35rem, 2.5vw, 2.4rem);
            line-height: 1.08;
            margin: 22px 0 20px;
            max-width: 62ch;
        }

        .lead,
        .helper,
        .side-note {
            color: var(--muted);
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 28px;
        }

        .hero-points {
            margin: 28px 0 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.9;
        }

        .form-panel {
            border-radius: var(--radius-xl);
            padding: 32px;
        }

        .form-panel h2 {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .status,
        .errors {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: var(--radius-md);
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

        form {
            display: grid;
            gap: 18px;
        }

        label,
        .checkbox {
            display: grid;
            gap: 8px;
            color: var(--ink);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .checkbox {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
        }

        .checkbox input {
            width: 16px;
            height: 16px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 16px;
            border-radius: 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, 0.76);
            color: var(--ink);
            font-size: 1rem;
        }

        input:focus {
            outline: 2px solid rgba(217, 106, 43, 0.22);
            outline-offset: 2px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .submit {
            cursor: pointer;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
        }

        .secondary {
            margin-top: 18px;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--muted);
        }

        .secondary a,
        .helper a {
            color: var(--accent-deep);
        }

        @media (max-width: 980px) {
            .page-shell {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 720px) {
            .brand__logo {
                height: 60px;
            }

            .hero-panel,
            .form-panel {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <x-hermes-header>
        <a class="pill" href="{{ $backHref }}">{{ $backLabel }}</a>
    </x-hermes-header>

    <main class="page-shell">
        <section class="hero-panel">
            <span class="eyebrow">{{ $eyebrow }}</span>
            <h1>{{ $heading }}</h1>
            <p class="lead">{{ $lead }}</p>

            @isset($heroActions)
                <div class="hero-actions">
                    {{ $heroActions }}
                </div>
            @endisset

            @if ($points !== [])
                <ul class="hero-points">
                    @foreach ($points as $point)
                        <li>{{ $point }}</li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="form-panel">
            <h2>{{ $formTitle }}</h2>
            <p class="helper">{{ $helper }}</p>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            {{ $slot }}

            @isset($secondary)
                <div class="secondary">{{ $secondary }}</div>
            @endisset

            @isset($sideNote)
                <p class="side-note">{{ $sideNote }}</p>
            @endisset
        </section>
    </main>

    <x-hermes-footer />
</body>
</html>
