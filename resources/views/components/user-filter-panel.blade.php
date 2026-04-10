@props([
    'tag' => 'section',
])

<{{ $tag }} {{ $attributes->class('user-filter-panel') }}>
    {{ $slot }}
</{{ $tag }}>
