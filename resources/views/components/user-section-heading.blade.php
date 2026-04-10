@props([
    'title' => null,
    'text' => null,
    'eyebrow' => null,
])

<div {{ $attributes->class('user-section-heading') }}>
    @if ($eyebrow)
        <span class="user-section-heading__eyebrow">{{ $eyebrow }}</span>
    @endif

    @if ($title)
        <h2>{{ $title }}</h2>
    @endif

    @if ($text)
        <p>{{ $text }}</p>
    @endif
</div>
