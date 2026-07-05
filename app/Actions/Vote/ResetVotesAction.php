<?php

declare(strict_types=1);

namespace App\Actions\Vote;

use App\Actions\Audit\ActionLog;
use App\Models\ActivityLog;
use App\Models\Election;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class ResetVotesAction
{
    use AsAction;

    public function handle(Election $election, ?int $voterId = null): array
    {
        $batchId = (string) Str::uuid();
        $setting = $election->setting;
        $originalStart = $setting->voting_start;
        $originalEnd = $setting->voting_end;

        $count = DB::transaction(function () use ($election, $voterId, $batchId): int {
            $query = Vote::query()
                ->where('election_id', $election->id);

            if ($voterId !== null) {
                $query->where('voter_id', $voterId);
            }

            $count = $query->count();

            $query->update([
                'reset_batch_id' => $batchId,
            ]);

            $query->delete();

            return $count;
        });

        $setting->update([
            'voting_start' => null,
            'voting_end' => null
        ]);

        ActionLog::run(
            'votes.reset',
            $election,
            [
                'reset_by' => Auth::id(),
                'election_id' => $election->id,
                'voting_start' => $originalStart,
                'voting_end' => $originalEnd,
                'voter_id' => $voterId,
                'votes_reset' => $count,
                'batch_id' => $batchId,
            ]
        );

        return [
            'status' => true,
            'batch_id' => $batchId,
            'message' => $voterId !== null
                ? "{$count} vote(s) reset for this voter."
                : "All {$count} vote(s) for \"{$election->name}\" have been reset.",
        ];
    }

    public function restore(Election $election, string $batchId): array
    {
        $setting = $election->setting;
        if ($election->votes->exists())         return [
            'status' => false,
            'message' => "Active vote found, you cannot restore vote at the moment.",
        ];
        $count = DB::transaction(function () use ($election, $batchId): int {
            $query = Vote::onlyTrashed()
                ->where('election_id', $election->id)
                ->where('reset_batch_id', $batchId);

            $count = $query->count();

            $query->restore();

            Vote::where('election_id', $election->id)
                ->where('reset_batch_id', $batchId)
                ->update([
                    'reset_batch_id' => null,
                ]);

            return $count;
        });
        // restore election settings (voting start & end) 
        $log = ActivityLog::query()
            ->where('action', 'votes.reset')
            ->where('properties->batch_id', $batchId)
            ->first();

        if ($log) {
            $setting->update([
                'voting_start' => $log->properties['voting_start'] ?? null,
                'voting_end' => $log->properties['voting_end'] ?? null,
            ]);
        }

        ActionLog::run(
            'votes.restore',
            $election,
            [
                'restored_by' => Auth::id(),
                'election_id' => $election->id,
                'batch_id' => $batchId,
                'votes_restored' => $count,
                'voting_start_restored' => $log->properties['voting_start'] ?? null,
                'voting_end_restored' => $log->properties['voting_end'] ?? null,
            ]
        );

        return [
            'status' => true,
            'message' => "{$count} vote(s) restored successfully.",
        ];
    }
}