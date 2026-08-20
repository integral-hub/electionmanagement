<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Vote\CastVoteAction;
use App\Http\Requests\CastVoteRequest;
use App\Models\Election;
use Illuminate\Http\JsonResponse;

class BallotController extends Controller
{
    public function ballot(Election $election): JsonResponse
    {
        $election->load(['positions.candidates' => function ($query) {
            $query->active();
        }, 'setting']);

        $voter = request()->user();

        if ($voter?->hasVote($election)) {
            return $this->fail('You have already voted in this election.', 409, [
                'redirect' => 'confirmation',
            ]);
        }

        return $this->success([
            'election' => $election,
            'voter' => $voter,
        ]);
    }

    public function cast(CastVoteRequest $request, Election $election): JsonResponse
    {
        $voter = request()->user();

        if ($voter?->hasVote($election)) {
            return $this->fail('You have already voted in this election.', 409);
        }

        CastVoteAction::run($election, $request->validated('votes'));

        return $this->success(null, 'Vote cast successfully.');
    }

    public function confirmation(Election $election): JsonResponse
    {
        $voter = request()->user();

        $election->load(['positions.candidates' => function ($query) {
            $query->active();
        }, 'setting']);

        $castVotes = $election->votes()->valid()
            ->where('voter_id', $voter->id)
            ->with(['position', 'candidate'])
            ->get();

        return $this->success([
            'election' => $election,
            'voter' => $voter,
            'cast_votes' => $castVotes,
        ]);
    }
}
