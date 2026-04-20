@props([
    'href' => route('home'),
    'showBooking' => false,
    'showContactLink' => true,
])

@php
    $currentLocale = app()->getLocale();
    $contactHref = route('contact.show', absolute: false);
    $localeNames = [
        'nl' => 'Nederlands',
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
    ];
@endphp

<style>
    .header-utility-link,
    .locale-menu__trigger,
    .locale-menu__item {
        font-family: Arial, Helvetica, sans-serif;
    }

    .header-utility-link,
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
    .locale-menu__trigger:hover {
        border-color: rgba(30, 71, 61, 0.18);
        background: rgba(255, 255, 255, 0.92);
    }

    .header-utility-link svg,
    .locale-menu__trigger svg {
        width: 25px;
        height: 25px;
        flex: 0 0 auto;
    }

    .locale-menu {
        position: relative;
    }

    .locale-menu__trigger {
        appearance: none;
    }

    .locale-menu__current {
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

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

    .locale-menu:hover .locale-menu__panel,
    .locale-menu:focus-within .locale-menu__panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
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
    .mobile-menu__submenu:not([open]) .mobile-menu__locale-list {
        display: none;
    }

    .mobile-menu__nav .home-submenu a {
        padding-left: 18px;
        font-size: 0.96rem;
        font-weight: 500;
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

                    @if ($showContactLink || $showBooking || trim((string) $slot) !== '')
                        <section class="mobile-menu__section" aria-label="Snelle acties">
                            <p class="mobile-menu__heading">{{ __('hermes.nav.actions') }}</p>

                            @if ($showContactLink)
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

            @if ($showContactLink)
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
