<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Election;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use App\Services\Interfaces\ElectionInterface;
use App\Repositories\Interfaces\ElectionSettingRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ElectionService implements ElectionInterface
{
    public function __construct(
        private readonly ElectionSettingRepositoryInterface $settingRepository
    ) {}

    public function create(array $data): Election
    {
        return DB::transaction(function () use ($data) {
            $this->ensureElectionLimitNotExceeded(global_data('org_id'));
            $election = Election::query()->create(
                array_merge($data, [
                    'organization_id' => global_data('org_id'),
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

        if ($result['status']) return $result;

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
    private function ensureElectionLimitNotExceeded(int $orgId): void
    {
        $organization = Organization::find($orgId);
        $maxElections = $organization->token->max_elections;

        if (is_null($maxElections)) {
            return; 
        }

        $currentCount = Election::where('organization_id', $organization->id)->count();

        if ($currentCount >= $maxElections) {
            throw ValidationException::withMessages([
                'organization_id' => 'You have reached the maximum number of elections allowed for your organization.',
            ]);
        }
    }
}