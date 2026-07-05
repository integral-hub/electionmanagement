<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Election;
use App\Models\ElectionSetting;

interface ElectionSettingRepositoryInterface
{
    public function create(Election $election, array $attributes = []): ElectionSetting;
    public function update(Election $election, array $attributes): ElectionSetting;
    
}