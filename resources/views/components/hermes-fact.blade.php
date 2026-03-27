@props([
    'title',
    'description',
])

<div {{ $attributes->class('fact') }}>
    <strong>{{ $title }}</strong>
    <span>{{ $description }}</span>
</div>
