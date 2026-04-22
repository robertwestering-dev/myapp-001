@props([
    'label',
    'value',
    'tone' => 'default',
])

<div {{ $attributes->class(['user-stat-tile', "user-stat-tile--{$tone}"]) }}>
    <strong>{{ $value }}</strong>
    <span>{{ $label }}</span>
</div>
