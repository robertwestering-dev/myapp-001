@props([
    'href' => route('home'),
    'showBooking' => true,
])

<header class="topbar">
    <div class="topbar__inner">
        <x-hermes-brand :href="$href" />

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
