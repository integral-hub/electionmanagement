<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Result\GetElectionResultsAction;
use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\Vote;

class ResultController extends Controller
{
    public function show(Election $election, GetElectionResultsAction $getElectionResults)
    {
        $this->authorize('view', Vote::class);

        $data = $getElectionResults->handle($election);

        return view('admin.results.show', [
            'election'         => $election,
            'results'          => $data['results'],
            'totalVotes'       => $data['totalVotes'],
            'eligibleVoters'   => $data['eligibleVoters'],
            'inEligibleVoters' => $data['inEligibleVoters'],
            'votedCount'       => $data['votedCount'],
            'turnoutPercent'   => $data['turnoutPercent'],
        ]);
    }
}