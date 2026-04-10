@props([
    'label',
    'value',
    'tone' => 'default',
])

<div {{ $attributes->class(['user-stat-tile', "user-stat-tile--{$tone}"]) }}>
    <span>{{ $label }}</span>
    <strong>{{ $value }}</strong>
</div>
