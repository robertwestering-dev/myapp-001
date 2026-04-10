@props([
    'align' => 'end',
])

@php
    $alignmentClass = $align === 'center' ? 'admin-toolbar--center' : 'admin-toolbar--end';
@endphp

<div {{ $attributes->class(['admin-toolbar', $alignmentClass]) }}>
    {{ $slot }}
</div>
