<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CastVoteRequest;
use App\Models\Election;
use App\Actions\Vote\CastVoteAction;
use Illuminate\Http\RedirectResponse;

class BallotController extends Controller
{

    public function ballot(Election $election)
    {
        $election->load(['positions.candidates' => function ($query) {
            $query->active(); }, 'setting',
        ]);
        
        $voter = voter();

        if ($voter?->hasVote($election)) {
            return redirect()->route('voter.confirmation', $election);
        }

        return view('voter.ballot', compact('election', 'voter'));
    }

    public function cast(CastVoteRequest $request, Election $election): RedirectResponse
    {
        if (voter()?->hasVote($election)) {
            return redirect()->route('voter.confirmation', $election);
        }

        CastVoteAction::run($election, $request->validated('votes'));

        return redirect()->route('voter.confirmation', $election);
    }

    public function confirmation(Election $election)
    {
        $voter = voter();
    
        $election->load(['positions.candidates' => function ($query) {
            $query->active(); }, 'setting',
        ]);

        $castVotes = $election->votes()->valid()
            ->where('voter_id', $voter->id)
            ->with(['position', 'candidate'])
            ->get();

        return view('voter.confirmation', compact('election', 'voter', 'castVotes'));
    }
}
