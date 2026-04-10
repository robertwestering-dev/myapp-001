@props([
    'href',
    'label',
    'title' => null,
    'variant' => 'default',
])

@php
    $classes = $variant === 'danger'
        ? 'danger-pill icon-button icon-button--danger'
        : 'ghost-pill icon-button';
@endphp

<a
    href="{{ $href }}"
    class="{{ $classes }}"
    aria-label="{{ $label }}"
    title="{{ $title ?? $label }}"
>
    {{ $slot }}
</a>
