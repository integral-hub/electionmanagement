<?php

declare(strict_types=1);

namespace App\Actions\Vote;

use App\Models\Vote;
use App\Models\Election;
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
            $voterId = Auth::id();

            foreach ($votes as $vote) {

                $positionId = $vote['position_id'];
                $candidateId = $vote['candidate_id'];

                // prevent duplicate vote per position per election per voter
                $exists = Vote::query()
                    ->where('election_id', $election->id)
                    ->where('position_id', $positionId)
                    ->where('voter_id', $voterId)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $created[] = Vote::query()->create([
                    'election_id' => $election->id,
                    'position_id' => $positionId,
                    'candidate_id' => $candidateId,
                    'voter_id' => $voterId,
                ]);
            }

            return $created;
        });
    }
}