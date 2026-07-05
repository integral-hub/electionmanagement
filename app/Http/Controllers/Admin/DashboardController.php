<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Dashboard\GetDashboardDataAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(GetDashboardDataAction $getDashboardData)
    {
        $org = Auth::user()->organization;

        $data = $getDashboardData->handle($org);

        return view('admin.dashboard', [
            'stats'           => $data['stats'],
            'recentElections' => $data['recentElections'],
            'recentLogs'      => $data['recentLogs'],
            'org'             => $org,
        ]);
    }
}
