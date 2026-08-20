<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Result\GetElectionResultsAction;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    public function show(Election $election, GetElectionResultsAction $getElectionResults): JsonResponse
    {
        $this->authorize('view', Vote::class);

        $data = $getElectionResults->handle($election);

        return $this->success([
            'election' => $election,
            'results' => $data['results'],
            'total_votes' => $data['totalVotes'],
            'eligible_voters' => $data['eligibleVoters'],
            'ineligible_voters' => $data['inEligibleVoters'],
            'voted_count' => $data['votedCount'],
            'turnout_percent' => $data['turnoutPercent'],
        ]);
    }
}
