@props([
    'href' => 'https://www.linkedin.com/in/robertwestering/',
    'label' => 'Zie mijn profiel op LinkedIn',
])

<a
    {{ $attributes->class('linkedin-profile-link') }}
    href="{{ $href }}"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="{{ $label }}"
>
    <span class="linkedin-profile-link__icon" aria-hidden="true">in</span>
    <span>{{ $label }}</span>
</a>
