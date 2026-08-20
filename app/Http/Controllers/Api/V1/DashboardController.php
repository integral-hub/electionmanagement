<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Dashboard\GetDashboardDataAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(GetDashboardDataAction $getDashboardData): JsonResponse
    {
        $org = Auth::user()->organization;

        $data = $getDashboardData->handle($org);

        return $this->success([
            'stats' => $data['stats'],
            'recent_elections' => $data['recentElections'],
            'recent_logs' => $data['recentLogs'],
            'organization' => $org,
        ]);
    }
}
