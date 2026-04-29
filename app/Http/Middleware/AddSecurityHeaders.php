<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = Str::random(32);

        // Vite adds nonce="" to every <script> tag it generates;
        // Livewire v4 reads Vite::cspNonce() automatically.
        Vite::useCspNonce($nonce);

        $response = $next($request);

        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy($request, $nonce));

        return $response;
    }

    private function contentSecurityPolicy(Request $request, string $nonce): string
    {
        if ($request->routeIs('academy-courses.show')) {
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
                "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
                "font-src 'self' data: https://fonts.bunny.net",
                "img-src 'self' data: https: blob:",
                "connect-src 'self' data: https: blob:",
                "media-src 'self' data: https: blob:",
                "worker-src 'self' blob:",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]);
        }

        return implode('; ', [
            "default-src 'self'",
            "script-src 'nonce-{$nonce}' 'strict-dynamic' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "font-src 'self' https://fonts.bunny.net",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);
    }
}
