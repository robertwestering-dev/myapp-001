@props([
    'align' => 'start',
])

<div {{ $attributes->class(['user-action-row', "user-action-row--{$align}"]) }}>
    {{ $slot }}
</div>
