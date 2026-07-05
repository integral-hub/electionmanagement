<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Election;
use Carbon\Carbon;
use App\Models\ElectionSetting;
use Illuminate\Validation\ValidationException;
use App\Repositories\Interfaces\ElectionSettingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ElectionSettingRepository implements ElectionSettingRepositoryInterface
{
    public function create(Election $election, array $attributes = []): ElectionSetting 
    {

        return $election->setting()->create($attributes);
    }

    public function update(Election $election, array $attributes): ElectionSetting
    {
        return DB::transaction(function () use ($election, $attributes) {
            $setting = $election->setting;
            if ($setting->voting_start && now()->gt($setting->voting_start) &&
                    array_key_exists('voting_start', $attributes) && $attributes['voting_start'] != $setting->voting_start) {

                    throw ValidationException::withMessages([
                        'voting_start' => 'Cannot adjust past voting start time.',
                    ]);
            }

            $setting = $setting->updateOrCreate(
                ['election_id' => $election->id],
                $attributes
            );

            if (isset($attributes['voting_start'])) {

                $start = $setting->voting_start;

                if ($start) {
                    $election->update([
                        'status' => $start->isFuture()
                            ? 'scheduled'
                            : 'running',
                    ]);
                }
            }
        
            if (isset($attributes['voting_end'])) {

                $end = $setting->voting_end;

                if ($election->status === 'completed' && $end && $end->isFuture()) {
                    
                    $election->update([
                        'status' => 'running',
                    ]);
                }
            }

            return $setting->refresh();
        });
    }

}