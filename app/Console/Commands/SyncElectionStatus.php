<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Election;

class SyncElectionStatus extends Command
{
    protected $signature = 'elections:sync-status';
    protected $description = 'Sync election status based on time rules';

    public function handle(): int
    {
        $now = now();

        $elections = Election::with('setting')->get();

        foreach ($elections as $election) {

            $setting = $election->setting;

            // 1. scheduled -> running
            if ($election->status === 'scheduled' && $setting->voting_start && $setting?->voting_start <= $now) {
                $election->status = 'running';
                $election->save();
                continue;
            }

            // 2. running -> completed
            if ($election->status === 'running' && $setting->voting_end && $setting->voting_end <= $now) {
                $election->status = 'completed';
                $election->save();
                continue;
            }

            // 3. scheduled -> draft (no start time)
            if ($election->status === 'scheduled' && is_null($setting->voting_start)) {
                $election->status = 'draft';
                $election->save();
                continue;
            }
        }

        return self::SUCCESS;
    }
}