@props([
    'title',
    'description' => null,
    'actionsClass' => 'actions',
])

<div {{ $attributes->class('empty') }}>
    <strong>{{ $title }}</strong>

    @if ($description)
        <div class="muted">{{ $description }}</div>
    @endif

    @isset($content)
        <div class="muted">{{ $content }}</div>
    @endisset

    @isset($actions)
        <div class="{{ $actionsClass }}">
            {{ $actions }}
        </div>
    @endisset
</div>
