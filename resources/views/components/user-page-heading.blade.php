@props([
    'title',
    'text' => null,
    'eyebrow' => null,
    'meta' => null,
])

<div {{ $attributes->class('user-page-heading') }}>
    @if ($eyebrow)
        <span class="user-page-heading__eyebrow">{{ $eyebrow }}</span>
    @endif

    <div class="user-page-heading__body">
        @if ($title)
            <h1>{{ $title }}</h1>
        @endif

        @if ($text)
            <p>{{ $text }}</p>
        @endif

        @if ($meta)
            <div class="user-page-heading__meta">
                {{ $meta }}
            </div>
        @endif
    </div>
</div>
