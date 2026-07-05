<?php

declare(strict_types=1);

namespace App\Actions\Election;

use App\Models\Election;
use App\Services\Interfaces\PortalChecklistInterface;
use Lorisleiva\Actions\Concerns\AsAction;

class GetElectionAction
{
    use AsAction;

    /**
     * @return array{election: Election, checklist: array, progress: int|float}
     */
    public function handle(Election $election, PortalChecklistInterface $checklistService, bool $withPositions = true): array
    {
        $checklist = $checklistService->checklist($election);
        $progress = $checklistService->progress($checklist);

        $election->load(array_filter([
            'setting',
            $withPositions ? 'positions.candidates' : null,
            $withPositions ? 'registrationField' : null,
        ]));

        if ($withPositions) {
            $election->loadCount([
                'voters',
                'votes as votes_count' => function ($q) {
                    $q->valid();
                },
            ]);
        }

        return [
            'election'  => $election,
            'checklist' => $checklist,
            'progress'  => $progress,
        ];
    }
}
