@props([
    'variant' => 'default',
    'tag' => 'article',
])

<{{ $tag }} {{ $attributes->class(['user-surface-card', "user-surface-card--{$variant}"]) }}>
    {{ $slot }}
</{{ $tag }}>
