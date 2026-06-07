<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Candidate;
use App\Services\Interfaces\CandidateInterface;

class CandidateService implements CandidateInterface
{
    public function create(array $data): Candidate
    {
        return Candidate::query()->create($data);
    }

    public function update(Candidate $candidate, array $data): Candidate 
    {

        $candidate->update($data);

        return $candidate->refresh();
    }

    public function delete(Candidate $candidate): array|bool 
    {

        $result = $this->canDelete($candidate);

        if ($result['status']) {
            return $result;
        }

        return (bool) $candidate->delete();
    }

    private function canDelete(Candidate $candidate): array {

        $status = $candidate->votes()->exists();

        return [
            'status' => $status,
            'message' => $status
                ? 'Candidate cannot be deleted because votes already exist.'
                : null,
        ];
    }
}