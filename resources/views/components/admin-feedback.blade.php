@props([
    'variant' => 'status',
    'messages' => [],
])

@php
    $messages = collect(is_iterable($messages) ? $messages : [$messages])->filter(fn ($message) => filled($message))->values();
@endphp

@if ($messages->isNotEmpty())
    <div {{ $attributes->class($variant === 'errors' ? 'errors' : 'status') }}>
        @foreach ($messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
