<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Election;
use Illuminate\Support\Facades\DB;
use App\Services\Interfaces\ElectionInterface;
use App\Repositories\Interfaces\ElectionSettingRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ElectionService implements ElectionInterface
{
    public function __construct(
        private readonly ElectionSettingRepositoryInterface $settingRepository
    ) {}

    public function create(array $data): Election
    {
        return DB::transaction(function () use ($data) {

            $election = Election::query()->create(
                array_merge($data, [
                    'created_by' => Auth::id(),
                ])
            );

            $this->settingRepository->create($election);

            return $election->load('setting');
        });
    }

    public function update(Election $election, array $data): Election 
    {

        $election->update($data);

        return $election->refresh();
    }

    public function delete(Election $election): array 
    {
        $result = $this->canDelete($election);

        if ($result['status']) {
            return $result;
        }

        $election->delete();

        return [
            'status' => true,
            'message' => 'Election deleted successfully.',
        ];
    }

    private function canDelete(Election $election): array
    {
        $blocked =
            $election->votes()->exists()
            || $election->positions()->exists()
            || $election->candidates()->exists();

        return [
            'status' => $blocked,
            'message' => $blocked
                ? 'Election cannot be deleted because it contains associated data. Contact support for assistance.'
                : null,
        ];
    }
}