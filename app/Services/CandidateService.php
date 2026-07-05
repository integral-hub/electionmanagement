<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Candidate;
use App\Services\Interfaces\CandidateInterface;
use App\Services\Interfaces\FileUploadInterface;
use Illuminate\Http\UploadedFile;

class CandidateService implements CandidateInterface
{
    public function __construct(
        private readonly FileUploadInterface $fileService
    ) {}

    public function create(array $data): Candidate
    {
        if ($photo = $this->upload($data['photo'] ?? null)) {
            $data['photo'] = $photo;
        }

        return Candidate::query()->create($data);
    }

    public function update(Candidate $candidate, array $data): Candidate
    {
        if ($photo = $this->upload($data['photo'] ?? null, $candidate)) {
            $data['photo'] = $photo;
        }

        $candidate->update($data);

        return $candidate->refresh();
    }

    public function delete(Candidate $candidate): array|bool
    {
        $result = $this->canDelete($candidate);

        if ($result['status']) return $result;

        if (!empty($candidate->photo['public_id'])) {
            $this->fileService->delete(
                $candidate->photo['public_id'],
                'image'
            );
        }

        return (bool) $candidate->delete();
    }

    private function canDelete(Candidate $candidate): array
    {
        $status = $candidate->votes()->exists();

        return [
            'status' => $status,
            'message' => $status
                ? 'Candidate cannot be deleted because votes already exist.'
                : null,
        ];
    }

    private function upload(?UploadedFile $image, ?Candidate $candidate = null): ?array 
    {
        if (!$image) return null;
        
        // Delete old image when updating
        if ( $candidate && !empty($candidate->photo['public_id'])) {

            $this->fileService->delete($candidate->photo['public_id'], 'image');
        }

        $upload = $this->fileService->upload($image, 'candidates');

        return $upload;
    }
}