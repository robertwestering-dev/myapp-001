<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<x-favicon-links />

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@php($viteManifestExists = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))

@if (! app()->runningUnitTests() && $viteManifestExists)
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
@fluxAppearance
