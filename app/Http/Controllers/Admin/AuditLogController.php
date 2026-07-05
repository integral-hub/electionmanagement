<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ActivityLog::class);
        
        $logs = ActivityLog::with('causer', 'subject')
                ->where('organization_id', global_data('org_id'))->latest()->paginate(30);

        return view('admin.audit-logs.index', compact('logs'));
    }
}