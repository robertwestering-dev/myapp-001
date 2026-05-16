@props([
    'href' => route('home'),
    'showBooking' => false,
    'showContactLink' => true,
])

@php
    $currentLocale = app()->getLocale();
    $contactHref = route('contact.show', absolute: false);
    $authenticatedUser = auth()->user();
    $localeNames = [
        'nl' => 'Nederlands',
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
    ];
@endphp

<style>
    .header-utility-link,
    .user-menu__trigger,
    .user-menu__item,
    .locale-menu__trigger,
    .locale-menu__item {
        font-family: Arial, Helvetica, sans-serif;
    }

    .header-utility-link,
    .user-menu__trigger,
    .locale-menu__trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 46px;
        padding: 10px 16px;
        border-radius: 999px;
        border: 1px solid var(--line, rgba(22, 33, 29, 0.12));
        background: rgba(255, 255, 255, 0.68);
        color: var(--ink, #16211d);
        box-shadow: var(--shadow, 0 24px 60px rgba(24, 34, 30, 0.14));
        font-size: 0.95rem;
        line-height: 1;
        white-space: nowrap;
        cursor: pointer;
    }

    .header-utility-link:hover,
    .user-menu__trigger:hover,
    .user-menu:focus-within .user-menu__trigger,
    .locale-menu__trigger:hover {
        border-color: rgba(30, 71, 61, 0.18);
        background: rgba(255, 255, 255, 0.92);
    }

    .header-utility-link svg,
    .user-menu__trigger svg,
    .locale-menu__trigger svg {
        width: 25px;
        height: 25px;
        flex: 0 0 auto;
    }

    .user-menu,
    .locale-menu {
        position: relative;
    }

    .user-menu__trigger,
    .locale-menu__trigger {
        appearance: none;
    }

    .locale-menu__current {
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .user-menu__panel,
    .locale-menu__panel {
        position: absolute;
        right: 0;
        top: calc(100% + 14px);
        min-width: 240px;
        padding: 14px;
        border-radius: 24px;
        border: 1px solid rgba(22, 33, 29, 0.1);
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 26px 60px rgba(24, 34, 30, 0.16);
        opacity: 0;
        visibility: hidden;
        transform: translateY(8px);
        transition: opacity 180ms ease, transform 180ms ease, visibility 180ms ease;
        z-index: 30;
    }

    .user-menu__panel::before,
    .locale-menu__panel::before {
        content: "";
        position: absolute;
        top: -10px;
        right: 28px;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.98);
        border-top: 1px solid rgba(22, 33, 29, 0.1);
        border-left: 1px solid rgba(22, 33, 29, 0.1);
        transform: rotate(45deg);
    }

    .user-menu:hover .user-menu__panel,
    .user-menu:focus-within .user-menu__panel,
    .locale-menu:hover .locale-menu__panel,
    .locale-menu:focus-within .locale-menu__panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .user-menu__item {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 13px 14px;
        border-radius: 18px;
        color: var(--ink, #16211d);
        font-size: 1.02rem;
        font-weight: 600;
        text-decoration: none;
    }

    .user-menu__item:hover,
    .user-menu__item:focus-visible {
        background: rgba(30, 71, 61, 0.08);
        outline: none;
    }

    .user-menu__item[aria-current="page"] {
        background: rgba(188, 91, 44, 0.1);
    }

    .user-menu__form {
        margin: 6px 0 0;
        position: relative;
        z-index: 1;
    }

    .user-menu__form .user-menu__item {
        appearance: none;
        width: 100%;
        border: 0;
        background: transparent;
        cursor: pointer;
        color: var(--ink, #16211d);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1.02rem;
        font-weight: 600;
        line-height: normal;
        text-align: left;
    }

    .user-menu__item svg {
        width: 21px;
        height: 21px;
        flex: 0 0 auto;
    }

    .locale-menu__form + .locale-menu__form {
        margin-top: 6px;
    }

    .locale-menu__item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 13px 14px;
        border: 0;
        border-radius: 18px;
        background: transparent;
        color: var(--ink, #16211d);
        font-size: 1.02rem;
        text-align: left;
        cursor: pointer;
    }

    .locale-menu__item:hover,
    .locale-menu__item:focus-visible {
        background: rgba(30, 71, 61, 0.08);
        outline: none;
    }

    .locale-menu__item[aria-current="true"] {
        background: rgba(188, 91, 44, 0.1);
    }

    .locale-menu__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(30, 71, 61, 0.12), rgba(188, 91, 44, 0.12));
        font-size: 0.84rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .mobile-menu {
        position: relative;
        display: none;
    }

    .mobile-menu__toggle {
        appearance: none;
        list-style: none;
    }

    .mobile-menu__toggle::-webkit-details-marker {
        display: none;
    }

    .mobile-menu__toggle,
    .mobile-menu__link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 46px;
        min-height: 46px;
        padding: 10px 16px;
        border-radius: 999px;
        border: 1px solid var(--line, rgba(22, 33, 29, 0.12));
        background: rgba(255, 255, 255, 0.68);
        color: var(--ink, #16211d);
        box-shadow: var(--shadow, 0 24px 60px rgba(24, 34, 30, 0.14));
        cursor: pointer;
    }

    .mobile-menu__toggle:hover,
    .mobile-menu__toggle:focus-visible,
    .mobile-menu__link:hover,
    .mobile-menu__link:focus-visible {
        border-color: rgba(30, 71, 61, 0.18);
        background: rgba(255, 255, 255, 0.92);
        outline: none;
    }

    .mobile-menu__toggle svg,
    .mobile-menu__link svg {
        width: 24px;
        height: 24px;
        flex: 0 0 auto;
    }

    .mobile-menu__panel {
        position: absolute;
        right: 0;
        top: calc(100% + 14px);
        width: min(340px, calc(100vw - 32px));
        max-height: calc(100dvh - 104px);
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        padding: 18px;
        border-radius: 28px;
        border: 1px solid rgba(22, 33, 29, 0.1);
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 26px 60px rgba(24, 34, 30, 0.18);
        display: grid;
        gap: 18px;
        z-index: 40;
    }

    .mobile-menu__section {
        display: grid;
        gap: 10px;
    }

    .mobile-menu__heading {
        margin: 0;
        color: var(--muted, #56655f);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .mobile-menu__nav,
    .mobile-menu__locale-list {
        display: grid;
        gap: 8px;
    }

    .mobile-menu__nav a,
    .mobile-menu__nav button.mobile-menu__account-link,
    .mobile-menu__nav .home-menu-trigger,
    .mobile-menu__locale-item {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 18px;
        border: 1px solid rgba(22, 33, 29, 0.08);
        background: rgba(244, 237, 227, 0.56);
        color: var(--ink, #16211d);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 1rem;
        font-weight: 600;
        text-align: left;
    }

    .mobile-menu__nav a:hover,
    .mobile-menu__nav a:focus-visible,
    .mobile-menu__nav button.mobile-menu__account-link:hover,
    .mobile-menu__nav button.mobile-menu__account-link:focus-visible,
    .mobile-menu__nav .home-menu-trigger:hover,
    .mobile-menu__nav .home-menu-trigger:focus-visible,
    .mobile-menu__locale-item:hover,
    .mobile-menu__locale-item:focus-visible {
        border-color: rgba(30, 71, 61, 0.18);
        background: rgba(255, 255, 255, 0.94);
        outline: none;
    }

    .mobile-menu__nav .home-menu-dropdown,
    .mobile-menu__submenu {
        display: grid;
        gap: 8px;
    }

    .mobile-menu__nav .home-menu-dropdown > summary,
    .mobile-menu__submenu > summary {
        list-style: none;
        cursor: pointer;
    }

    .mobile-menu__nav .home-menu-dropdown > summary::-webkit-details-marker,
    .mobile-menu__submenu > summary::-webkit-details-marker {
        display: none;
    }

    .mobile-menu__nav .home-submenu {
        position: static;
        min-width: 0;
        padding: 0 0 0 14px;
        border: 0;
        background: transparent;
        box-shadow: none;
        display: grid;
        gap: 8px;
        opacity: 1;
        visibility: visible;
        transform: none;
    }

    .mobile-menu__nav .home-menu-dropdown:not([open]) .home-submenu,
    .mobile-menu__account:not([open]) .mobile-menu__account-list,
    .mobile-menu__submenu:not([open]) .mobile-menu__locale-list {
        display: none;
    }

    .mobile-menu__nav .home-submenu a {
        padding-left: 18px;
        font-size: 0.96rem;
        font-weight: 500;
    }

    .mobile-menu__account-list {
        display: grid;
        gap: 8px;
    }

    .mobile-menu__account-form {
        margin: 0;
    }

    .mobile-menu__account-form button.mobile-menu__account-link {
        width: 100%;
        border: 1px solid rgba(22, 33, 29, 0.08);
        cursor: pointer;
        justify-content: space-between;
    }

    .mobile-menu__locale-form {
        margin: 0;
    }

    .mobile-menu__locale-item[aria-current="true"] {
        background: rgba(188, 91, 44, 0.1);
    }

    .mobile-menu__locale-meta {
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }

    .mobile-menu__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(30, 71, 61, 0.12), rgba(188, 91, 44, 0.12));
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .mobile-menu__panel .nav-actions,
    .mobile-menu__panel .nav-actions form {
        display: grid;
        gap: 10px;
    }

    .mobile-menu__panel .pill,
    .mobile-menu__panel button[type="submit"] {
        width: 100%;
        justify-content: center;
    }

    @media (max-width: 720px) {
        .header-utility-link,
        .user-menu__trigger,
        .locale-menu__trigger {
            padding: 10px 14px;
        }

        .locale-menu__current {
            display: none;
        }

        .locale-menu__panel {
            right: -8px;
            min-width: 220px;
        }
    }

    @media (max-width: 780px) {
        .mobile-menu {
            display: block;
        }

        .mobile-menu[open] .mobile-menu__panel {
            position: fixed;
            top: 88px;
            right: 16px;
            width: min(340px, calc(100vw - 32px));
        }

        .topbar__menu,
        .topbar__actions > :not(.mobile-menu) {
            display: none;
        }
    }
</style>

<header class="topbar">
    <div class="topbar__inner">
        <div class="topbar__left">
            <x-hermes-brand :href="$href" />

            @isset($menu)
                @if (trim((string) $menu) !== '')
                    <nav class="topbar__menu" aria-label="Hoofdmenu">
                        {{ $menu }}
                    </nav>
                @endif
            @endisset
        </div>

        <div class="topbar__actions">
            <details class="mobile-menu">
                <summary class="mobile-menu__toggle" aria-label="Open navigatiemenu">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 7h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M4 12h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M4 17h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </summary>

                <div class="mobile-menu__panel">
                    @isset($menu)
                        @if (trim((string) $menu) !== '')
                            <section class="mobile-menu__section" aria-label="Mobiele navigatie">
                                <p class="mobile-menu__heading">{{ __('hermes.nav.navigation') }}</p>
                                <nav class="mobile-menu__nav">
                                    {{ $menu }}
                                </nav>
                            </section>
                        @endif
                    @endisset

                    @if ($showContactLink && $authenticatedUser)
                        <section class="mobile-menu__section" aria-label="Gebruikersmenu mobiel">
                            <details class="mobile-menu__submenu mobile-menu__account">
                                <summary class="mobile-menu__locale-item">
                                    <span class="mobile-menu__locale-meta">
                                        <span class="mobile-menu__badge">
                                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 21.25a9.25 9.25 0 1 0 0-18.5 9.25 9.25 0 0 0 0 18.5Z" stroke="currentColor" stroke-width="1.7"/>
                                                <path d="M12 12.35a3.05 3.05 0 1 0 0-6.1 3.05 3.05 0 0 0 0 6.1Z" stroke="currentColor" stroke-width="1.7"/>
                                                <path d="M5.82 18.88c.44-3.33 2.72-5.38 6.18-5.38s5.74 2.05 6.18 5.38" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                            </svg>
                                        </span>
                                        <span>Persoonlijk</span>
                                    </span>
                                    <span aria-hidden="true">▾</span>
                                </summary>

                                <nav class="mobile-menu__nav mobile-menu__account-list">
                                    <a class="mobile-menu__account-link" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>{{ __('hermes.dashboard.title') }}</a>
                                    <a class="mobile-menu__account-link" href="{{ route('journal.timeline') }}" @if (request()->routeIs('journal.timeline')) aria-current="page" @endif>{{ __('hermes.journal.timeline_page_title') }}</a>
                                    <a class="mobile-menu__account-link" href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.edit')) aria-current="page" @endif>{{ __('hermes.nav.profile') }}</a>
                                    <a class="mobile-menu__account-link" href="{{ $contactHref }}">{{ __('hermes.nav.contact') }}</a>
                                    <form method="POST" action="{{ route('logout') }}" class="mobile-menu__account-form">
                                        @csrf
                                        <button type="submit" class="mobile-menu__account-link">{{ __('hermes.dashboard.logout') }}</button>
                                    </form>
                                </nav>
                            </details>
                        </section>
                    @endif

                    <section class="mobile-menu__section" aria-label="Taalmenu mobiel">
                        <details class="mobile-menu__submenu">
                            <summary class="mobile-menu__locale-item">
                                <span class="mobile-menu__locale-meta">
                                    <span class="mobile-menu__badge">{{ strtoupper($currentLocale) }}</span>
                                    <span>{{ __('hermes.locales.switcher_label') }}</span>
                                </span>
                                <span aria-hidden="true">▾</span>
                            </summary>

                            <div class="mobile-menu__locale-list">
                                @foreach (config('locales.supported', []) as $localeCode => $localeLabel)
                                    <form method="POST" action="{{ route('locale.update') }}" class="mobile-menu__locale-form">
                                        @csrf
                                        <input type="hidden" name="locale" value="{{ $localeCode }}">
                                        <button
                                            type="submit"
                                            class="mobile-menu__locale-item"
                                            @if ($currentLocale === $localeCode) aria-current="true" @endif
                                        >
                                            <span class="mobile-menu__locale-meta">
                                                <span class="mobile-menu__badge">{{ strtoupper($localeCode) }}</span>
                                                <span>{{ $localeNames[$localeCode] ?? $localeLabel }}</span>
                                            </span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </details>
                    </section>

                    @if (($showContactLink && ! $authenticatedUser) || $showBooking || trim((string) $slot) !== '')
                        <section class="mobile-menu__section" aria-label="Snelle acties">
                            <p class="mobile-menu__heading">{{ __('hermes.nav.actions') }}</p>

                            @if ($showContactLink && ! $authenticatedUser)
                                <a
                                    class="mobile-menu__link"
                                    href="{{ $contactHref }}"
                                    aria-label="Ga naar contactformulier"
                                    title="Contact"
                                >
                                    <span>Contact</span>
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M3.75 6.75h16.5a1.5 1.5 0 0 1 1.5 1.5v7.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                        <path d="m3.75 8.25 7.33 5.52a1.5 1.5 0 0 0 1.84 0l7.33-5.52" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            @endif

                            @if ($showBooking)
                                <a
                                    class="pill pill--booking"
                                    href="https://calendly.com/robertwestering/30min"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('hermes.header.booking') }}
                                </a>
                            @endif

                            @if (trim((string) $slot) !== '')
                                {{ $slot }}
                            @endif
                        </section>
                    @endif
                </div>
            </details>

            @if ($showContactLink && $authenticatedUser)
                <div class="user-menu">
                    <button type="button" class="user-menu__trigger" aria-label="Open gebruikersmenu">
                        <svg class="user-menu__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 21.25a9.25 9.25 0 1 0 0-18.5 9.25 9.25 0 0 0 0 18.5Z" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M12 12.35a3.05 3.05 0 1 0 0-6.1 3.05 3.05 0 0 0 0 6.1Z" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M5.82 18.88c.44-3.33 2.72-5.38 6.18-5.38s5.74 2.05 6.18 5.38" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </button>

                    <nav class="user-menu__panel" aria-label="Gebruikersmenu">
                        <a class="user-menu__item" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5.75A1.75 1.75 0 0 1 5.75 4h4.5A1.75 1.75 0 0 1 12 5.75v4.5A1.75 1.75 0 0 1 10.25 12h-4.5A1.75 1.75 0 0 1 4 10.25v-4.5Z" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M14 5.75A1.75 1.75 0 0 1 15.75 4h2.5A1.75 1.75 0 0 1 20 5.75v12.5A1.75 1.75 0 0 1 18.25 20h-2.5A1.75 1.75 0 0 1 14 18.25V5.75Z" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M4 15.75A1.75 1.75 0 0 1 5.75 14h4.5A1.75 1.75 0 0 1 12 15.75v2.5A1.75 1.75 0 0 1 10.25 20h-4.5A1.75 1.75 0 0 1 4 18.25v-2.5Z" stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                            <span>{{ __('hermes.dashboard.title') }}</span>
                        </a>
                        <a class="user-menu__item" href="{{ route('journal.timeline') }}" @if (request()->routeIs('journal.timeline')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 5.5h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M7 12h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M7 18.5h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M4 5.5h.01M4 12h.01M4 18.5h.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                            </svg>
                            <span>{{ __('hermes.journal.timeline_page_title') }}</span>
                        </a>
                        <a class="user-menu__item" href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.edit')) aria-current="page" @endif>
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 12.25a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M5.75 19.25c.58-3.35 2.9-5.25 6.25-5.25s5.67 1.9 6.25 5.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            <span>{{ __('hermes.nav.profile') }}</span>
                        </a>
                        <a class="user-menu__item" href="{{ $contactHref }}">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 6.75h14A1.75 1.75 0 0 1 20.75 8.5v7A2.75 2.75 0 0 1 18 18.25H6A2.75 2.75 0 0 1 3.25 15.5v-7A1.75 1.75 0 0 1 5 6.75Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="m4 8.25 6.98 5.08a1.75 1.75 0 0 0 2.04 0L20 8.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ __('hermes.nav.contact') }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="user-menu__form">
                            @csrf
                            <button type="submit" class="user-menu__item">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M9.75 5.25H6.5A1.75 1.75 0 0 0 4.75 7v10A1.75 1.75 0 0 0 6.5 18.75h3.25" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    <path d="M13 8.25 16.75 12 13 15.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16.5 12H8.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                                <span>{{ __('hermes.dashboard.logout') }}</span>
                            </button>
                        </form>
                    </nav>
                </div>
            @elseif ($showContactLink)
                <a
                    class="header-utility-link"
                    href="{{ $contactHref }}"
                    aria-label="Ga naar contactformulier"
                    title="Contact"
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3.75 6.75h16.5a1.5 1.5 0 0 1 1.5 1.5v7.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25v-7.5a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="m3.75 8.25 7.33 5.52a1.5 1.5 0 0 0 1.84 0l7.33-5.52" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif

            <div class="locale-menu">
                <button type="button" class="locale-menu__trigger" aria-label="Open taalmenu">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3.75a8.25 8.25 0 1 0 0 16.5 8.25 8.25 0 0 0 0-16.5Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M3.75 12h16.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M12 3.75c2.22 2.18 3.47 5.15 3.47 8.25S14.22 18.07 12 20.25c-2.22-2.18-3.47-5.15-3.47-8.25S9.78 5.93 12 3.75Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    <span class="locale-menu__current">{{ strtoupper($currentLocale) }}</span>
                </button>

                <div class="locale-menu__panel" role="menu" aria-label="Taalmenu">
                    @foreach (config('locales.supported', []) as $localeCode => $localeLabel)
                        <form method="POST" action="{{ route('locale.update') }}" class="locale-menu__form">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $localeCode }}">
                            <button
                                type="submit"
                                class="locale-menu__item"
                                @if ($currentLocale === $localeCode) aria-current="true" @endif
                            >
                                <span class="locale-menu__badge">{{ strtoupper($localeCode) }}</span>
                                <span>{{ $localeNames[$localeCode] ?? $localeLabel }}</span>
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>

            @if ($showBooking)
                <a
                    class="pill pill--booking"
                    href="https://calendly.com/robertwestering/30min"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    {{ __('hermes.header.booking') }}
                </a>
            @endif

            @if (trim((string) $slot) !== '')
                {{ $slot }}
            @endif
        </div>
    </div>
</header>
