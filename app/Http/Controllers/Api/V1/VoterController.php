<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\ImportExport\DownloadImportTemplate;
use App\Actions\ImportExport\ImportVoters;
use App\Actions\ImportExport\ViewImportLogs;
use App\Actions\Voter\AssignVoterAction;
use App\Actions\Voter\ListAssignableVotersAction;
use App\Actions\Voter\ListElectionVotersAction;
use App\Enums\VoterStatusEnum;
use App\Http\Requests\Election\AssignVoterRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Requests\Voter\CreateRequest;
use App\Http\Requests\Voter\EditRequest;
use App\Models\Election;
use App\Models\Voter;
use App\Models\VotersImportLog;
use App\Services\Interfaces\VoterInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    public function __construct(
        private readonly VoterInterface $service
    ) {}

    public function index(Request $request, Election $election, ListElectionVotersAction $listElectionVoters): JsonResponse
    {
        $this->authorize('viewAny', Voter::class);

        $voters = $listElectionVoters->handle($election, $request->query('q'), $request->query('status'));

        return $this->success($voters);
    }

    public function show(Election $election, Voter $voter): JsonResponse
    {
        $this->authorize('view', $voter);

        $voter->load(['uniqueData', 'organization']);

        $fieldDefs = collect($election->registrationField?->fields ?? [])
            ->keyBy('field_name');

        $pivot = $election->voters()
            ->where('voter_id', $voter->id)
            ->first()?->pivot;

        return $this->success([
            'voter' => $voter,
            'field_defs' => $fieldDefs,
            'pivot' => $pivot,
        ]);
    }

    public function store(CreateRequest $request, Election $election): JsonResponse
    {
        $this->authorize('create', Voter::class);

        $voter = $this->service->create($election, $request->validated());

        return $this->success($voter, 'Voter added successfully.', 201);
    }

    public function assignable(Request $request, Election $election, ListAssignableVotersAction $listAssignableVoters): JsonResponse
    {
        $this->authorize('assign', Voter::class);

        $voters = $listAssignableVoters->handle(
            $election,
            $request->input('batch_code'),
            $request->input('q')
        );

        return $this->success($voters);
    }

    public function storeAssign(AssignVoterRequest $request, Election $election): JsonResponse
    {
        $this->authorize('assign', Voter::class);

        $result = AssignVoterAction::run(
            $election,
            $request->input('voters', [])
        );

        return $this->success($result, "{$result['assigned']} voter(s) assigned.");
    }

    public function update(EditRequest $request, Election $election, Voter $voter): JsonResponse
    {
        $this->authorize('update', $voter);

        $voter = $this->service->editVoter($election, $voter, $request->validated());

        return $this->success($voter, 'Voter updated.');
    }

    public function destroy(Election $election, Voter $voter): JsonResponse
    {
        $this->authorize('delete', $voter);

        $voter->elections()->detach($election->id);

        return $this->success(null, 'Voter removed from election.');
    }

    public function approve(Election $election, Voter $voter): JsonResponse
    {
        $this->authorize('approve', $voter);

        $this->service->updateValidationStatus(
            $election,
            $voter,
            VoterStatusEnum::Validated->value,
            true
        );

        return $this->success(null, 'Voter approved.');
    }

    public function reject(Election $election, Voter $voter): JsonResponse
    {
        $this->authorize('reject', $voter);

        $this->service->updateValidationStatus(
            $election,
            $voter,
            VoterStatusEnum::Banned->value,
            false
        );

        return $this->success(null, 'Voter rejected.');
    }

    public function import(ImportRequest $request, Election $election): JsonResponse
    {
        $this->authorize('import', Voter::class);

        ImportVoters::run($election, $request->file('file'));

        return $this->success(null, 'Import started. A summary will be emailed to you when complete.');
    }

    public function downloadTemplate(Election $election)
    {
        $this->authorize('import', Voter::class);

        return DownloadImportTemplate::run($election);
    }

    public function importLogs(Election $election): JsonResponse
    {
        $this->authorize('view', VotersImportLog::class);

        $logs = ViewImportLogs::run($election);

        return $this->success($logs);
    }

    public function previewFile(Election $election, Voter $voter, string $field): JsonResponse
    {
        $this->authorize('view', $voter);

        $url = data_get($voter->voter_data, "{$field}.url");

        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->fail('File not found.', 404);
        }

        return $this->success(['url' => $url]);
    }
}
