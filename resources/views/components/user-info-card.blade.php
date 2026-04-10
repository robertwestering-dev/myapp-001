@props([
    'title',
    'text',
    'badge' => null,
    'tone' => 'default',
])

<article {{ $attributes->class(['user-info-card', "user-info-card--{$tone}"]) }}>
    @if ($badge)
        <span class="user-info-card__badge">{{ $badge }}</span>
    @endif

    <strong>{{ $title }}</strong>
    <p>{{ $text }}</p>
</article>
