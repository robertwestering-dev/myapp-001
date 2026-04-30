@props([
    'title',
])

@php
    $activeMenu = match (true) {
        request()->routeIs('dashboard') => 'dashboard',
        request()->routeIs('questionnaires.index'), request()->routeIs('questionnaire-responses.*') => 'questionnaires',
        request()->routeIs('academy.index') => 'academy',
        request()->routeIs('journal.*') => 'journal',
        request()->routeIs('forum.*'), request()->routeIs('forum-replies.*') => 'forum',
        request()->routeIs('blog.*') => 'blog',
        request()->routeIs('profile.*') => 'profile',
        default => null,
    };
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')

    <style>
        :root {
            --ink: #16211d;
            --muted: #5a6762;
            --line: rgba(22, 33, 29, 0.12);
            --accent: #d96a2b;
            --accent-deep: #a84a19;
            --shadow: 0 24px 60px rgba(24, 34, 30, 0.14);
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

        .topbar__inner {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .topbar__left,
        .topbar__actions {
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

        .topbar__menu a:hover,
        .topbar__menu a[aria-current='page'] {
            color: var(--accent-deep);
        }

        .brand__logo {
            display: block;
            width: auto;
            height: 54px;
            max-width: 100%;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(16px);
            background: rgba(244, 239, 230, 0.78);
            border-bottom: 1px solid rgba(22, 33, 29, 0.08);
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

        .user-feedback {
            display: grid;
            gap: 8px;
            padding: 16px 18px;
            border-radius: 20px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.96rem;
            line-height: 1.6;
        }

        .user-feedback--status {
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.18);
            color: var(--ink);
        }

        .user-feedback--errors {
            background: rgba(217, 106, 43, 0.1);
            border: 1px solid rgba(168, 74, 25, 0.2);
            color: var(--accent-deep);
        }

        .user-feedback--subtle {
            background: rgba(255, 255, 255, 0.65);
            border: 1px solid rgba(22, 33, 29, 0.08);
            color: var(--muted);
        }

        .user-guidance-card {
            display: grid;
            gap: 16px;
            padding: 24px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid rgba(22, 33, 29, 0.12);
            box-shadow: var(--shadow);
        }

        .user-guidance-card--accent {
            background:
                linear-gradient(145deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
            border-color: rgba(255, 255, 255, 0.08);
            color: #f6efe5;
        }

        .user-guidance-card__eyebrow {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #2f5f52;
        }

        .user-guidance-card--accent .user-guidance-card__eyebrow {
            color: rgba(246, 239, 229, 0.82);
        }

        .user-guidance-card--accent .user-guidance-card__body strong {
            color: #f6efe5;
        }

        .user-guidance-card__body {
            display: grid;
            gap: 10px;
        }

        .user-guidance-card__body strong {
            font-size: 1.1rem;
            line-height: 1.2;
        }

        .user-guidance-card__body p {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--muted);
            line-height: 1.6;
        }

        .user-guidance-card--accent .user-guidance-card__body p {
            color: rgba(246, 239, 229, 0.82);
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
            border: 1px solid rgba(22, 33, 29, 0.12);
            box-shadow: var(--shadow);
        }

        .user-surface-card--accent {
            background:
                linear-gradient(145deg, rgba(32, 69, 58, 0.96), rgba(20, 37, 32, 0.97)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
            border-color: rgba(255, 255, 255, 0.08);
            color: #f6efe5;
        }

        .user-surface-card--accent .user-page-heading__eyebrow,
        .user-surface-card--accent .user-section-heading__eyebrow,
        .user-surface-card--accent .user-page-heading h1,
        .user-surface-card--accent .user-section-heading h2,
        .user-surface-card--accent .user-page-heading p,
        .user-surface-card--accent .user-section-heading p,
        .user-surface-card--accent .user-page-heading__meta,
        .user-surface-card--accent .user-inline-meta {
            color: #f6efe5;
        }

        .user-surface-card--accent .user-page-heading__eyebrow,
        .user-surface-card--accent .user-section-heading__eyebrow,
        .user-surface-card--accent .user-page-heading p,
        .user-surface-card--accent .user-section-heading p,
        .user-surface-card--accent .user-page-heading__meta,
        .user-surface-card--accent .user-inline-meta {
            color: rgba(246, 239, 229, 0.82);
        }

        .user-surface-card--accent .user-inline-meta span + span::before {
            color: rgba(246, 239, 229, 0.42);
        }

        .user-surface-card--soft {
            background: rgba(255, 255, 255, 0.58);
        }

        .user-stat-tile {
            display: grid;
            gap: 8px;
            padding: 18px;
            border-radius: 22px;
            background: rgba(32, 69, 58, 0.08);
            border: 1px solid rgba(32, 69, 58, 0.12);
        }

        .user-stat-tile span {
            font-family: Arial, Helvetica, sans-serif;
            color: #5a6762;
            line-height: 1.6;
        }

        .user-stat-tile strong {
            font-size: 2rem;
            line-height: 1.05;
        }

        .user-surface-card--accent .user-stat-tile {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .user-surface-card--accent .user-stat-tile span {
            color: rgba(246, 239, 229, 0.82);
        }

        .user-stat-tile--warning {
            background: rgba(217, 106, 43, 0.1);
            border-color: rgba(217, 106, 43, 0.16);
        }

        .user-page-heading,
        .user-section-heading {
            display: grid;
            gap: 10px;
        }

        .user-page-heading__eyebrow,
        .user-section-heading__eyebrow {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #2f5f52;
        }

        .user-page-heading__body,
        .user-page-heading__meta {
            display: grid;
            gap: 10px;
        }

        .user-page-heading h1,
        .user-section-heading h2 {
            margin: 0;
            color: var(--ink);
            line-height: 1.08;
        }

        .user-page-heading h1 {
            font-size: clamp(1.3rem, 2.5vw, 2.4rem);
        }

        .user-section-heading h2 {
            font-size: clamp(1.1rem, 2vw, 1.7rem);
        }

        .user-page-heading p,
        .user-section-heading p,
        .user-page-heading__meta {
            margin: 0;
            max-width: 62ch;
            color: var(--muted);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            line-height: 1.6;
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
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.94rem;
            color: var(--muted);
        }

        .user-inline-meta span {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .user-inline-meta span + span::before {
            content: '•';
            color: rgba(22, 33, 29, 0.3);
        }

        .user-inline-meta--light {
            color: rgba(246, 239, 229, 0.82);
        }

        .user-inline-meta--light span + span::before {
            color: rgba(246, 239, 229, 0.42);
        }

        .user-filter-panel {
            display: grid;
            gap: 18px;
            padding: 28px;
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .user-panel {
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
        }

        .user-panel--padded {
            padding: 34px;
        }

        .user-panel--compact {
            padding: 32px;
        }

        .user-info-grid {
            display: grid;
            gap: 20px 24px;
        }

        .user-info-grid--2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .user-info-grid--3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .user-info-grid--4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .user-info-card {
            display: grid;
            gap: 8px;
            padding: 18px 20px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(22, 33, 29, 0.08);
        }

        .user-info-card strong {
            color: #20453a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
        }

        .user-info-card p {
            margin: 0;
            color: #5a6762;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            line-height: 1.6;
        }

        .user-info-card .user-info-card__prompt {
            margin-top: 8px;
            color: var(--accent);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.5;
        }

        .user-info-card__badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(32, 69, 58, 0.09);
            color: #20453a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .user-info-card--warning .user-info-card__badge {
            background: rgba(217, 106, 43, 0.12);
            color: #a84a19;
        }

        .user-info-card--contrast {
            border-color: rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.08);
        }

        .user-info-card--contrast strong,
        .user-info-card--contrast p {
            color: rgba(246, 242, 235, 0.82);
        }

        .user-info-card--contrast strong {
            color: #f6f2eb;
        }

        .user-meta-grid {
            display: grid;
            gap: 16px;
        }

        .user-meta-grid--2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .user-meta-grid--3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .user-meta-item {
            display: grid;
            gap: 6px;
        }

        .user-meta-item dt {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            font-weight: 700;
            color: #2f5f52;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .user-meta-item dd {
            margin: 0;
            color: #5a6762;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        .page-shell {
            display: grid;
            gap: 24px;
        }

        main {
            flex: 1;
            padding: 48px 0 64px;
        }

        .site-footer {
            background: rgba(244, 239, 230, 0.78);
            border-top: 1px solid rgba(22, 33, 29, 0.08);
        }

        .site-footer__inner {
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.95rem;
            color: var(--muted);
            line-height: 1.2;
        }

        @media (max-width: 780px) {
            .topbar__inner {
                height: 80px;
                min-height: 80px;
                padding: 12px 0;
                align-items: center;
                flex-direction: row;
            }

            .user-info-grid--2,
            .user-info-grid--3,
            .user-info-grid--4,
            .user-meta-grid--2,
            .user-meta-grid--3 {
                grid-template-columns: 1fr;
            }

            .topbar__left {
                min-width: 0;
                flex: 1;
                flex-wrap: nowrap;
                gap: 12px;
            }

            .topbar__menu {
                margin-left: 0;
            }

            .topbar__actions {
                width: auto;
                justify-content: flex-end;
                flex: 0 0 auto;
            }

            .brand__logo {
                height: 46px;
            }
        }

        @media (max-width: 720px) {
            .user-panel--padded {
                padding: 26px;
            }

            .user-panel--compact {
                padding: 24px;
            }
        }
    </style>

    {{ $head ?? '' }}
</head>
<body>
    <x-hermes-header :href="route('dashboard')" :show-booking="false">
        <x-slot:menu>
            <a href="{{ route('dashboard') }}" @if ($activeMenu === 'dashboard') aria-current="page" @endif>{{ __('hermes.dashboard.title') }}</a>
            <a href="{{ route('questionnaires.index') }}" @if ($activeMenu === 'questionnaires') aria-current="page" @endif>{{ __('hermes.nav.questionnaires') }}</a>
            <a href="{{ route('academy.index') }}" @if ($activeMenu === 'academy') aria-current="page" @endif>{{ __('hermes.nav.academy') }}</a>
            @if (auth()->user()?->isProUser())
                <a href="{{ route('journal.index') }}" @if ($activeMenu === 'journal') aria-current="page" @endif>{{ __('hermes.nav.journal') }}</a>
            @endif
            <a href="{{ route('forum.index') }}" @if ($activeMenu === 'forum') aria-current="page" @endif>{{ __('hermes.nav.forum') }}</a>
            <a href="{{ route('blog.index') }}" @if ($activeMenu === 'blog') aria-current="page" @endif>{{ __('hermes.nav.blog') }}</a>
            <a href="{{ route('profile.edit') }}" @if ($activeMenu === 'profile') aria-current="page" @endif>{{ __('hermes.nav.profile') }}</a>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('admin.portal') }}">{{ __('hermes.admin_menu.portal') }}</a>
            @endif
        </x-slot:menu>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="pill pill--neutral">{{ __('hermes.dashboard.logout') }}</button>
        </form>
    </x-hermes-header>

    <main>
        <div class="page-shell">
            {{ $slot }}
        </div>
    </main>

    <x-hermes-footer />

    @fluxScripts
</body>
</html>
