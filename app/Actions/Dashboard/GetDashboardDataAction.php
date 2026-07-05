<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Models\ActivityLog;
use App\Models\Election;
use App\Models\Organization;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDashboardDataAction
{
    use AsAction;

    /**
     * @return array{stats: array<string,int>, recentElections: Collection, recentLogs: Collection}
     */
    public function handle(Organization $org): array
    {
        $stats = [
            'elections'    => Election::query()->where('organization_id', $org->id)->count(),
            'active'       => Election::query()->where('organization_id', $org->id)->where('status', 'running')->count(),
            'total_voters' => Voter::query()->where('organization_id', $org->id)->count(),
            'staff'        => User::query()->where('organization_id', $org->id)->count(),
        ];

        $recentElections = Election::query()->where('organization_id', $org->id)
            ->with('setting')
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = ActivityLog::query()->where('organization_id', $org->id)
            ->latest()
            ->take(5)
            ->get();

        return [
            'stats'           => $stats,
            'recentElections' => $recentElections,
            'recentLogs'      => $recentLogs,
        ];
    }
}
