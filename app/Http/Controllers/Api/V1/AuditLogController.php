<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;

class AuditLogController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', ActivityLog::class);

        $logs = ActivityLog::with('causer', 'subject')
            ->where('organization_id', global_data('org_id'))
            ->latest()
            ->paginate(30);

        return $this->success($logs);
    }
}
