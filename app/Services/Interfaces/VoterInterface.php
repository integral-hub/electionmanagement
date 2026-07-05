<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Election;
use App\Models\Voter;

interface VoterInterface
{
    public function create(Election $election, array $data): Voter;
    public function update(Voter $voter, array $data): Voter;
    public function editVoter(Election $election, Voter $voter, array $data): Voter;
    public function updateValidationStatus(Election $election, Voter $voter, string $status, bool $isValid): void;
}