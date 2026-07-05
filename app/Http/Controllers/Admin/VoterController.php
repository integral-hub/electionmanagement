<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\ImportExport\DownloadImportTemplate;
use App\Actions\ImportExport\ImportVoters;
use App\Actions\ImportExport\ViewImportLogs;
use App\Actions\Voter\AssignVoterAction;
use App\Actions\Voter\ListAssignableVotersAction;
use App\Actions\Voter\ListElectionVotersAction;
use App\Enums\VoterStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Election\AssignVoterRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Requests\Voter\CreateRequest;
use App\Http\Requests\Voter\EditRequest;
use App\Models\Election;
use App\Models\Voter;
use App\Models\VotersImportLog;
use App\Services\Interfaces\VoterInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    public function __construct(
        private readonly VoterInterface $service
    ) {}

    public function index(Election $election, ListElectionVotersAction $listElectionVoters)
    {
        $this->authorize('viewAny', Voter::class);

        $voters = $listElectionVoters->handle($election, request('q'), request('status'));

        return view('admin.voters.index', compact('election', 'voters'));
    }

    public function show(Election $election, Voter $voter)
    {
        $this->authorize('view', $voter);

        $voter->load(['uniqueData', 'organization']);

        $fieldDefs = collect($election->registrationField?->fields ?? [])
            ->keyBy('field_name');

        $pivot = $election->voters()
            ->where('voter_id', $voter->id)
            ->first()?->pivot;

        return view('admin.voters.show', compact(
            'election',
            'voter',
            'fieldDefs',
            'pivot'
        ));
    }

    public function create(Election $election)
    {
        $this->authorize('create', Voter::class);

        $fields = $election->registrationField?->fields ?? [];

        return view('admin.voters.create', compact('election', 'fields'));
    }

    public function store(CreateRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('create', Voter::class);

        $this->service->create($election, $request->validated());

        return redirect()
            ->route('admin.elections.voters.index', $election)
            ->with('success', 'Voter added successfully.');
    }

    public function assign(Request $request, Election $election, ListAssignableVotersAction $listAssignableVoters)
    {
        $this->authorize('assign', Voter::class);

        $voters = $listAssignableVoters->handle(
            $election,
            $request->input('batch_code'),
            $request->input('q')
        );

        return view('admin.voters.assign', compact('election', 'voters'));
    }

    public function storeAssign(AssignVoterRequest $request, Election $election): RedirectResponse 
    {
        $this->authorize('assign', Voter::class);

        $result = AssignVoterAction::run(
            $election,
            $request->input('voters', [])
        );

        return redirect()
            ->route('admin.elections.voters.index', $election)
            ->with('success', "{$result['assigned']} voter(s) assigned.");
    }

    public function edit(Election $election, Voter $voter)
    {
        $this->authorize('update', $voter);

        $fields = $election->registrationField?->fields ?? [];

        return view('admin.voters.create', compact(
            'election',
            'voter',
            'fields'
        ));
    }

    public function update(EditRequest $request, Election $election, Voter $voter): RedirectResponse 
    {
        $this->authorize('update', $voter);

        $this->service->editVoter(
            $election,
            $voter,
            $request->validated()
        );

        return redirect()
            ->route('admin.elections.voters.show', [$election, $voter])
            ->with('success', 'Voter updated.');
    }

    public function destroy(Election $election, Voter $voter): RedirectResponse
    {
        $this->authorize('delete', $voter);

        $voter->elections()->detach($election->id);

        return redirect()
            ->route('admin.elections.voters.index', $election)
            ->with('success', 'Voter removed from election.');
    }

    public function approve(Election $election, Voter $voter): RedirectResponse
    {
        $this->authorize('approve', $voter);

        $this->service->updateValidationStatus(
            $election,
            $voter,
            VoterStatusEnum::Validated->value,
            true
        );

        return back()->with('success', 'Voter approved.');
    }

    public function reject(Election $election, Voter $voter): RedirectResponse
    {
        $this->authorize('reject', $voter);

        $this->service->updateValidationStatus(
            $election,
            $voter,
            VoterStatusEnum::Banned->value,
            false
        );

        return back()->with('success', 'Voter rejected.');
    }

    public function import(ImportRequest $request, Election $election): RedirectResponse 
    {
        $this->authorize('import', Voter::class);

        ImportVoters::run(
            $election,
            $request->file('file')
        );

        return back()->with(
            'success',
            'Import started. A summary will be emailed to you when complete.'
        );
    }

    public function export(Election $election)
    {
       // $this->authorize('view', Voter::class);

    }

    public function downloadTemplate(Election $election)
    {
        $this->authorize('import', Voter::class);

        return DownloadImportTemplate::run($election);
    }

    public function importLogs(Election $election)
    {
        $this->authorize('view', VotersImportLog::class);

        $logs = ViewImportLogs::run($election);

        return view('admin.voters.import-logs', compact('election', 'logs'));
    }

    public function previewFile(Election $election, Voter $voter, string $field) 
    {
        $this->authorize('view', $voter);

        $url = data_get($voter->voter_data, "{$field}.url");

        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            abort(404, 'File not found.');
        }

        return redirect()->away($url);
    }
}
