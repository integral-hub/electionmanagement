<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Election;
use App\Models\Voter;

interface VoterInterface
{
    public function create(Election $election, array $data): Voter;
}