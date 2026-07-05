<?php

declare(strict_types=1);

namespace App\Actions\ImportExport;

use App\Models\Election;
use App\Models\VotersImportLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class ViewImportLogs
{
    use AsAction;

    public function handle(Election $election, int $perPage = 20): LengthAwarePaginator
    {
        return VotersImportLog::where('election_id', $election->id)
            ->with('uploader:id,name,email')
            ->latest()
            ->paginate($perPage);
    }
}
