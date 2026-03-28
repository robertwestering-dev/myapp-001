@php($pngVersion = file_exists(public_path('favicon.png')) ? filemtime(public_path('favicon.png')) : null)
@php($icoVersion = file_exists(public_path('favicon.ico')) ? filemtime(public_path('favicon.ico')) : null)
@php($appleTouchVersion = file_exists(public_path('apple-touch-icon.png')) ? filemtime(public_path('apple-touch-icon.png')) : null)
@php($pngHref = asset('favicon.png').($pngVersion ? '?v='.$pngVersion : ''))
@php($icoHref = asset('favicon.ico').($icoVersion ? '?v='.$icoVersion : ''))
@php($appleTouchHref = asset('apple-touch-icon.png').($appleTouchVersion ? '?v='.$appleTouchVersion : ''))

<link rel="icon" href="{{ $pngHref }}" type="image/png">
<link rel="alternate icon" href="{{ $icoHref }}" sizes="any" type="image/x-icon">
<link rel="shortcut icon" href="{{ $icoHref }}" type="image/x-icon">
<link rel="apple-touch-icon" href="{{ $appleTouchHref }}">
