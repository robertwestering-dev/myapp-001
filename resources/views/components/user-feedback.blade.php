@props([
    'variant' => 'status',
    'messages' => [],
    'alwaysRender' => false,
])

@php
    $messages = collect(is_iterable($messages) ? $messages : [$messages])->filter(fn ($message) => filled($message))->values();
@endphp

@if ($alwaysRender || $messages->isNotEmpty())
    <div {{ $attributes->class(['user-feedback', "user-feedback--{$variant}"]) }}>
        @foreach ($messages as $message)
            <div>{{ $message }}</div>
        @endforeach

        {{ $slot }}
    </div>
@endif
