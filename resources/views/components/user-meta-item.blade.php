@props([
    'label',
    'value',
])

<div {{ $attributes->class('user-meta-item') }}>
    <dt>{{ $label }}</dt>
    <dd>{{ $value }}</dd>
</div>
