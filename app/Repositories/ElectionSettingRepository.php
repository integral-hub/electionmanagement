<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Election;
use App\Models\ElectionSetting;
use App\Repositories\Interfaces\ElectionSettingRepositoryInterface;

class ElectionSettingRepository implements ElectionSettingRepositoryInterface
{
    public function create(Election $election, array $attributes = []): ElectionSetting 
    {

        return $election->setting()->create($attributes);
    }

    public function update(ElectionSetting $setting, array $attributes): ElectionSetting 
    {
        $setting->update($attributes);

        return $setting->refresh();
    }
}