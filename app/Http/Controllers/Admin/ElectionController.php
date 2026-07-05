<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Election\GetElectionAction;
use App\Actions\Election\ListElectionAction;
use App\Enums\ElectionStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Election\CreateRequest;
use App\Http\Requests\Election\EditRequest;
use App\Http\Requests\Election\EditSettingsRequest;
use App\Models\Election;
use App\Repositories\ElectionSettingRepository;
use App\Services\Interfaces\ElectionInterface;
use App\Services\Interfaces\PortalChecklistInterface;
use Illuminate\Http\RedirectResponse;

class ElectionController extends Controller
{
    public function __construct(
        private readonly ElectionInterface $service,
        private readonly ElectionSettingRepository $settingRepo,
        private readonly PortalChecklistInterface $checklistService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Election::class);

        $elections = ListElectionAction::run();

        return view('admin.elections.index', compact('elections'));
    }

    public function create()
    {
        $this->authorize('create', Election::class);

        return view('admin.elections.create');
    }

    public function store(CreateRequest $request): RedirectResponse
    {
        $this->authorize('create', Election::class);

        $election = $this->service->create($request->validated());

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Election created successfully.');
    }

    public function show(Election $election, GetElectionAction $getElectionShow)
    {
        $this->authorize('view', $election);

        $data = $getElectionShow->handle($election, $this->checklistService);

        return view('admin.elections.show', $data);
    }

    public function edit(Election $election)
    {
        $this->authorize('update', $election);
        
        $statuses = ElectionStatusEnum::selected();

        return view('admin.elections.edit', compact('election', 'statuses'));
    }

    public function update(EditRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('update', $election);

        $this->service->update($election, $request->validated());

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Election updated successfully.');
    }

    public function destroy(Election $election): RedirectResponse
    {
        $this->authorize('delete', $election);

        $result = $this->service->delete($election);

        if ($result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.elections.index')
            ->with('success', 'Election deleted.');
    }

    public function settings(Election $election)
    {
        $this->authorize('view', $election);

        $checklist = $this->checklistService->checklist($election);
        $progress = $this->checklistService->progress($checklist);

        $election->load('setting');

        return view('admin.elections.settings', compact('election', 'progress'));
    }

    public function updateSettings(EditSettingsRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('update', $election);

        $this->settingRepo->update($election, $request->validated());

        return back()->with('success', 'Settings saved.');
    }
}