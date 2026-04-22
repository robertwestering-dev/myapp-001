<?php

namespace App\Actions\Fortify;

use App\Services\SuccessfulLoginSummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function __construct(private readonly SuccessfulLoginSummary $successfulLoginSummary) {}

    public function toResponse($request): JsonResponse|Response
    {
        /** @var Request $request */
        $user = $request->user();

        if ($user !== null) {
            $this->successfulLoginSummary->record($request, $user);

            if (! $user->canAccessAdminPortal() && ! $user->isProfileComplete()) {
                Session::flash('profile_incomplete_prompt', true);

                return redirect()->route('profile.edit');
            }
        }

        return redirect()->intended(
            $user?->canAccessAdminPortal()
                ? route('admin.portal', absolute: false)
                : route('dashboard', absolute: false)
        );
    }
}
