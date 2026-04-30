<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public function __construct(private readonly Request $request) {}

    public function log(AuditAction $action, string $description, ?Model $subject = null): AdminActivityLog
    {
        return AdminActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action->value,
            'subject_type' => $subject ? $subject->getMorphClass() : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'ip_address' => $this->request->ip(),
        ]);
    }
}
