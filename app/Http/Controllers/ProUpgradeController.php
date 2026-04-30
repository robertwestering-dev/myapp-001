<?php

namespace App\Http\Controllers;

use App\Enums\AuditAction;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProUpgradeController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // Atomic update: only upgrades when the role is still ROLE_USER at query time,
        // preventing duplicate upgrades from concurrent requests.
        $upgraded = DB::table('users')
            ->where('id', $user->id)
            ->where('role', User::ROLE_USER)
            ->update(['role' => User::ROLE_USER_PRO]);

        if ($upgraded > 0) {
            $user->role = User::ROLE_USER_PRO;
            $this->audit->log(AuditAction::UserProUpgrade, "Gebruiker geüpgraded naar Pro: {$user->name} ({$user->email})", $user);
        }

        return redirect()
            ->route('pro-upgrade.show')
            ->with('status', __('hermes.pro_upgrade_page.upgraded_status'));
    }
}
