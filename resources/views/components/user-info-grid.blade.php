@props([
    'columns' => '2',
])

<div {{ $attributes->class(['user-info-grid', "user-info-grid--{$columns}"]) }}>
    {{ $slot }}
</div>
