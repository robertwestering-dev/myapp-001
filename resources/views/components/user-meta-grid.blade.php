@props([
    'columns' => '2',
])

<div {{ $attributes->class(['user-meta-grid', "user-meta-grid--{$columns}"]) }}>
    {{ $slot }}
</div>
