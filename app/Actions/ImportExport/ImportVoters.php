<?php

declare(strict_types=1);

namespace App\Actions\ImportExport;

use App\Imports\VotersImport;
use App\Models\Election;
use App\Models\VotersImportLog;
use App\Notifications\ImportCompleted;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;
use Maatwebsite\Excel\Facades\Excel;

class ImportVoters
{
    use AsAction;

    public function handle(Election $election, UploadedFile $file): VotersImportLog 
    {
        $batchCode = 'BATCH-' . strtoupper(substr(md5(uniqid()), 0, 8));
        
        $importLog = VotersImportLog::create([
            'election_id'   => $election->id,
            'file_name'     => $file->getClientOriginalName(),
            'total_records' => 0,
            'batch_code'    => $batchCode,
            'uploaded_by'   => Auth::id(),
        ]);

       $import = new VotersImport($election, $batchCode);

        Excel::import($import, $file);

        $stats = $import->getStats();

        $importLog->update([
            'total_records' => $stats['imported'],
        ]);

        $importLog->uploader->notify(
            new ImportCompleted(
                election: $election,
                importLog: $importLog->refresh(),
                batchCode: $stats['batchcode'],
                imported: $stats['imported'],
                failed: $stats['failed'],
                rowErrors: $stats['errors'],
            )
        );

        return $importLog->refresh();
    }
}