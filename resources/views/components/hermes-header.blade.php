@props([
    'href' => route('home'),
    'showBooking' => true,
])

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
            @if ($showBooking)
                <a
                    class="pill pill--booking"
                    href="https://calendly.com/robertwestering/30min"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Maak een afspraak
                </a>
            @endif

            @if (trim((string) $slot) !== '')
                {{ $slot }}
            @endif
        </div>
    </div>
</header>
