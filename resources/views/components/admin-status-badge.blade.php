@props([
    'label',
    'tone' => 'default',
    'uppercase' => false,
])

<span {{ $attributes->class([
    'admin-status-badge',
    'admin-status-badge--warning' => $tone === 'warning',
    'admin-status-badge--danger' => $tone === 'danger',
    'admin-status-badge--neutral' => $tone === 'neutral',
    'admin-status-badge--uppercase' => $uppercase,
]) }}>
    {{ $label }}
</span>
