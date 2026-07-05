<?php

declare(strict_types=1);

namespace App\Actions\Vote;

use App\Models\Vote;
use App\Models\Election;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class CastVoteAction
{
    use AsAction;

public function handle(Election $election, array $votes): array
{
    return DB::transaction(function () use ($election, $votes) {

        $created = [];
        $voterId = voter()->id;
        
        //Collect all position IDs from request
        $positionIds = collect($votes)
            ->pluck('position_id')
            ->unique()
            ->values();

        // Load all positions
        $positions = Position::query()
            ->whereIn('id', $positionIds)
            ->get()
            ->keyBy('id');


        // counters
        $voteCounts = [];
        $candidateExists = [];


        // Process votes
        foreach ($votes as $vote) {

            $positionId = $vote['position_id'];
            $candidateId = $vote['candidate_id'];

            $position = $positions[$positionId] ?? null;
            if (!$position) {
                continue;
            }

            $maxVotes = $position->max_votes ?? 1;

            // max vote check
            if (($voteCounts[$positionId] ?? 0) >= $maxVotes) {
                continue;
            }

            // duplicate candidate vote check
            if (!empty($candidateExists[$positionId][$candidateId])) {
                continue;
            }

            $created[] = Vote::query()->create([
                'election_id' => $election->id,
                'position_id' => $positionId,
                'candidate_id' => $candidateId,
                'voter_id' => $voterId,
            ]);

            // update counters
            $voteCounts[$positionId] = ($voteCounts[$positionId] ?? 0) + 1;
            $candidateExists[$positionId][$candidateId] = true;
        }

        return $created;
    });
}
}