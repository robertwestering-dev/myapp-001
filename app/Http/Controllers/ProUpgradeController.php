<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProUpgradeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->role === User::ROLE_USER) {
            $user->forceFill([
                'role' => User::ROLE_USER_PRO,
            ])->save();
        }

        return redirect()
            ->route('pro-upgrade.show')
            ->with('status', __('hermes.pro_upgrade_page.upgraded_status'));
    }
}
