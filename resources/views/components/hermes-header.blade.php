@props([
    'href' => route('home'),
])

<header class="topbar">
    <div class="topbar__inner">
        <x-hermes-brand :href="$href" />

        @if (trim((string) $slot) !== '')
            <div class="topbar__actions">
                {{ $slot }}
            </div>
        @endif
    </div>
</header>
