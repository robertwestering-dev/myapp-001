@props([
    'href' => route('home'),
])

<a {{ $attributes->class('brand')->merge(['href' => $href]) }}>
    <img
        src="{{ asset('images/hermes-results-logo.png') }}"
        alt="Hermes Results"
        class="brand__logo"
    >
</a>
