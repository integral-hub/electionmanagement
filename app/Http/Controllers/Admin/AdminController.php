<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Vote\ResetVotesAction;
use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function reset(Election $election, ResetVotesAction $action): RedirectResponse
    {
        $this->authorize('reset', $election);

        $result = $action->handle($election);

        return back()->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function resetForVoter(Election $election, int $voterId, ResetVotesAction $action): RedirectResponse
    {
        $this->authorize('reset', $election);

        $result = $action->handle($election, $voterId);

        return back()->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function restore(Election $election, string $batchId, ResetVotesAction $action): RedirectResponse
    {
        $this->authorize('restore', $election);

        $result = $action->restore($election, $batchId);

        return back()->with(
            $result['status'] ? 'success' : 'error',
            $result['message']
        );
    }
}