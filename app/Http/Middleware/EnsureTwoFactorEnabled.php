<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user && $user->canAccessAdminPortal() && ($user->two_factor_secret === null || $user->two_factor_confirmed_at === null)) {
            return redirect()->route('admin.two-factor.notice');
        }

        return $next($request);
    }
}
