<?php

declare(strict_types=1);

namespace App\Actions\Voter;

use App\Models\Election;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ListElectionVotersAction
{
    use AsAction;

    public function handle(Election $election, ?string $search = null, ?string $status = null): LengthAwarePaginator
    {
        $query = $election->voters()
            ->withPivot('status', 'validated_by', 'validated_at');

        if ($search) {
            $query->where(fn ($s) => $s
                ->where('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            );
        }

        if ($status) {
            $query->wherePivot('status', $status);
        }

        return $query->paginate(20)->withQueryString();
    }
}
