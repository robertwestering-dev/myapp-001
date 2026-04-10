@props([
    'title',
    'text',
    'eyebrow' => null,
    'actionLabel' => null,
    'actionHref' => null,
    'variant' => 'default',
])

<article {{ $attributes->class(['user-guidance-card', "user-guidance-card--{$variant}"]) }}>
    @if ($eyebrow)
        <span class="user-guidance-card__eyebrow">{{ $eyebrow }}</span>
    @endif

    <div class="user-guidance-card__body">
        <strong>{{ $title }}</strong>
        <p>{{ $text }}</p>
    </div>

    @if ($actionLabel && $actionHref)
        <div class="user-guidance-card__actions">
            <a href="{{ $actionHref }}" class="pill">{{ $actionLabel }}</a>
        </div>
    @endif
</article>
