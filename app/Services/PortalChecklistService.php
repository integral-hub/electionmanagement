<?php

namespace App\Services;

use App\Models\Election;
use App\Services\Interfaces\PortalChecklistInterface;

class PortalChecklistService implements PortalChecklistInterface
{
    public function checklist(Election $election): array
    {
        $baseChecklist = [
            
            'positions' => [
                'label' => 'Positions',
                'done'  => $election->positions()->exists(),
                'hint'  => 'Add at least one position (e.g. President)',
                'route' => route('admin.elections.positions.create', $election),
                'action' => 'Add Position',
            ],

            'candidates' => [
                'label' => 'Candidates',
                'done'  => $election->candidates()->exists(),
                'hint'   => $election->positions()->exists()
                    ? 'Add candidates to each position'
                    : 'Create at least one position first',
                'route'  => $election->positions()->exists()
                    ? route('admin.elections.candidates.create', $election)
                    : null,
                'action' => $election->positions()->exists()
                    ? 'Add Candidate'
                    : 'Position Required',
            ],

            'registration' => [
                'label' => $election->setting?->registration_mode === 'open'
                    ? 'Registration Form'
                    : 'Voters List',

                'done' => $election->setting?->registration_mode === 'open'
                    ? (bool) $election->registrationField
                    : $election->voters()->exists(),

                'hint' => $election->setting?->registration_mode === 'open'
                    ? 'Build voter registration form'
                    : 'Open registration mode or Import voters to this election',

                'route' => $election->setting?->registration_mode === 'open'
                    ? route('admin.elections.registration.show', $election)
                    : route('admin.elections.voters.index', $election),

                'action' => $election->setting?->registration_mode === 'open'
                    ? 'Setup Form'
                    : 'Manage Voters',
            ],
        ];

        // Check if ALL non-date tasks are done
        $allCoreDone = collect($baseChecklist)->every(fn ($item) => $item['done']);

        // Voting dates only becomes active when others are done
        $baseChecklist['voting_dates'] = [
            'done'  => $dateSet = $election->setting?->voting_start && $election->setting?->voting_end,
            'label' => !$dateSet ? 'Voting Schedule' : 'Adjust Date Setting',

            'hint'  => $allCoreDone
                ? 'Set start and end time for voting'
                : null,

            'route' => $allCoreDone
                ? route('admin.elections.settings', $election)
                : null,

            'action' => !$dateSet ? 'Set Dates' : 'Adjust Dates',
            // visibility flag (used in Blade)
            'visible' => $allCoreDone,
        ];

        return $baseChecklist;
    }

    public function progress(array $checklist): int
    {
        $total = count($checklist);
        $done = collect($checklist)->where('done', true)->count();

        return $total ? (int) round(($done / $total) * 100) : 0;
    }
}