<?php

declare(strict_types=1);

namespace App\Actions\Election;

use App\Models\Election;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ListElectionAction
{
    use AsAction;

    public function handle(): LengthAwarePaginator
    {
        return Election::where('organization_id', global_data('org_id'))
            ->with(['setting', 'creator'])
            ->withCount([
                'positions',
                'candidates',
                'voters',
                'votes as votes_count' => function ($q) {
                    $q->valid();
                },
            ])
            ->latest()
            ->paginate(12);
    }
}