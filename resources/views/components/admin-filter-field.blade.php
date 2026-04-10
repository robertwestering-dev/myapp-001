@props([
    'label',
    'for' => null,
    'description' => null,
])

<div {{ $attributes->class('admin-filter-field') }}>
    <label @if ($for) for="{{ $for }}" @endif class="admin-filter-field__label">
        {{ $label }}
    </label>

    @if ($description)
        <div class="admin-filter-field__description">{{ $description }}</div>
    @endif

    {{ $slot }}
</div>
