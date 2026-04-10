@props([
    'items' => [],
    'tone' => 'default',
])

@php
    $items = collect(is_iterable($items) ? $items : [$items])->filter(fn ($item) => filled($item))->values();
    $hasSlotContent = trim((string) $slot) !== '';
@endphp

@if ($items->isNotEmpty() || $hasSlotContent)
    <div {{ $attributes->class(['user-inline-meta', "user-inline-meta--{$tone}"]) }}>
        @if ($items->isNotEmpty())
            @foreach ($items as $item)
                <span>{{ $item }}</span>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </div>
@endif
