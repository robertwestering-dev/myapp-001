<?php

namespace App\Listeners;

use App\Enums\AuditAction;
use App\Models\AdminActivityLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        try {
            AdminActivityLog::create([
                'user_id' => null,
                'action' => AuditAction::LoginFailed->value,
                'description' => 'Mislukte loginpoging voor: '.($event->credentials['email'] ?? 'onbekend'),
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable) {
            // never block the login flow
        }
    }
}
