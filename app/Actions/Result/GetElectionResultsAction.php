<?php

declare(strict_types=1);

namespace App\Actions\Result;

use App\Models\Election;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetElectionResultsAction
{
    use AsAction;

    /**
     * @return array{results: Collection, totalVotes: int, eligibleVoters: int, inEligibleVoters: int, votedCount: int, turnoutPercent: float}
     */
    public function handle(Election $election): array
    {
        $election->load(['positions.candidates.votes', 'setting']);

        $results = $election->positions->map(function ($position) use ($election) {
            $totalVotes = $position->votes()->valid()->where('election_id', $election->id)->count();

            $candidates = $position->candidates->map(function ($candidate) use ($election, $totalVotes) {
                $votes = $candidate->votes()->valid()->where('election_id', $election->id)->count();

                return [
                    'candidate' => $candidate,
                    'votes'     => $votes,
                    'percent'   => $totalVotes > 0 ? round(($votes / $totalVotes) * 100, 1) : 0,
                ];
            })->sortByDesc('votes')->values();

            return [
                'position'    => $position,
                'candidates'  => $candidates,
                'total_votes' => $totalVotes,
            ];
        });

        $eligibleStatuses = $election->setting?->vote_before_validation
            ? ['validated', 'pending']
            : ['validated'];

        $eligibleVoters = $election->voters()->wherePivotIn('status', $eligibleStatuses)->count();
        $inEligibleVoters = $election->voters()->wherePivot('status', 'banned')->count();
        $votedCount = $election->votes()->valid()->distinct('voter_id')->count('voter_id');

        return [
            'results'          => $results,
            'totalVotes'       => (int) $results->sum('total_votes'),
            'eligibleVoters'   => $eligibleVoters,
            'inEligibleVoters' => $inEligibleVoters,
            'votedCount'       => $votedCount,
            'turnoutPercent'   => $eligibleVoters > 0 ? round(($votedCount / $eligibleVoters) * 100, 1) : 0,
        ];
    }
}