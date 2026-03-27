@props([
    'tagline',
    'heading',
    'description' => null,
])

<div {{ $attributes->class('section-head') }}>
    <div>
        <div class="tagline">{{ $tagline }}</div>
        <h2>{{ $heading }}</h2>
    </div>

    @if ($description)
        <p>{{ $description }}</p>
    @endif
</div>
