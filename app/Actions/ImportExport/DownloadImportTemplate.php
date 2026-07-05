<?php

declare(strict_types=1);

namespace App\Actions\ImportExport;

use App\Exports\VotersTemplateExport;
use App\Models\Election;
use Lorisleiva\Actions\Concerns\AsAction;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadImportTemplate
{
    use AsAction;

    public function handle(Election $election): BinaryFileResponse
    {
        $filename = sprintf('voter_import_template.xlsx');
        return Excel::download(new VotersTemplateExport($election), $filename);
    }
}
