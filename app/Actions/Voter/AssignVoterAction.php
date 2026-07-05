<?php

declare(strict_types=1);

namespace App\Actions\Voter;

use App\Enums\VoterStatusEnum;
use App\Models\Election;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Bulk-assign voters to an election.
 *
 * Accepts an array of voter IDs. and attach to election
 *
 */
class AssignVoterAction
{
    use AsAction;

    public function handle(Election $election, array $voterIds): array
    {
        $now = now();
        $userId = Auth::id();

        $data = [];

        foreach ($voterIds as $voterId) {
            $data[$voterId] = [
                'status'       => VoterStatusEnum::Validated->value,
                'validated_by' => $userId,
                'validated_at' => $now,
            ];
        }

        $election->voters()->attach($data);

        return ['assigned' => count($voterIds)];
    }
}
