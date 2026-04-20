<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->value();
        $action = $request->string('action')->trim()->value();

        $logs = AdminActivityLog::query()
            ->with('user:id,name,email')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('description', 'like', "%{$search}%");
            })
            ->when($action !== '', function ($query) use ($action): void {
                $query->where('action', $action);
            })
            ->latest()
            ->paginate(config('app.per_page'))
            ->withQueryString();

        $actions = AdminActivityLog::query()
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'search' => $search,
            'selectedAction' => $action,
            'actions' => $actions,
        ]);
    }
}
