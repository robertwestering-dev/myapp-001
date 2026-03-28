@php($faviconVersion = file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : null)
@php($faviconHref = asset('favicon.ico').($faviconVersion ? '?v='.$faviconVersion : ''))

<link rel="icon" href="{{ $faviconHref }}" sizes="any" type="image/x-icon">
<link rel="shortcut icon" href="{{ $faviconHref }}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{ $faviconHref }}">
