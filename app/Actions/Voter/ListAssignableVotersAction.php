<?php

declare(strict_types=1);

namespace App\Actions\Voter;

use App\Models\Election;
use App\Models\Voter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ListAssignableVotersAction
{
    use AsAction;

    public function handle(Election $election, ?string $batchCode = null, ?string $search = null): LengthAwarePaginator
    {
        $query = Voter::query()
            ->with('uniqueData')
            ->where('organization_id', global_data('org_id') ?? null)
            ->whereDoesntHave('elections', function ($q) use ($election) {
                $q->where('elections.id', $election->id);
            })
            ->whereHas('elections', function ($q) {
                $q->where('election_voters.status', 'validated');
            });

        if ($batchCode) {
            $query->where('batch_code', $batchCode);
        }

        if ($search) {
            $query->where(fn ($s) => $s
                ->where('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            );
        }

        return $query->paginate(30)->withQueryString();
    }
}
