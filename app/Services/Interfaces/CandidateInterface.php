<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Candidate;

interface CandidateInterface
{
    public function create(array $data): Candidate;
    public function update(Candidate $candidate, array $data): Candidate;
    public function delete(Candidate $candidate): array|bool;
}