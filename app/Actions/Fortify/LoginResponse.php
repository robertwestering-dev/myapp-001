<?php

namespace App\Actions\Fortify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse|Response
    {
        /** @var Request $request */
        $user = $request->user();

        return redirect()->intended(
            $user?->canAccessAdminPortal()
                ? route('admin.portal', absolute: false)
                : route('dashboard', absolute: false)
        );
    }
}
