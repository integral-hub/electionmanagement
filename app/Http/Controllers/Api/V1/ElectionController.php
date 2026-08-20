<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Election\GetElectionAction;
use App\Actions\Election\ListElectionAction;
use App\Http\Requests\Election\CreateRequest;
use App\Http\Requests\Election\EditRequest;
use App\Http\Requests\Election\EditSettingsRequest;
use App\Models\Election;
use App\Repositories\ElectionSettingRepository;
use App\Services\Interfaces\ElectionInterface;
use App\Services\Interfaces\PortalChecklistInterface;
use Illuminate\Http\JsonResponse;

class ElectionController extends Controller
{
    public function __construct(
        private readonly ElectionInterface $service,
        private readonly ElectionSettingRepository $settingRepo,
        private readonly PortalChecklistInterface $checklistService
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Election::class);

        $elections = ListElectionAction::run();

        return $this->success($elections);
    }

    public function store(CreateRequest $request): JsonResponse
    {
        $this->authorize('create', Election::class);

        $election = $this->service->create($request->validated());

        return $this->success($election, 'Election created successfully.', 201);
    }

    public function show(Election $election, GetElectionAction $getElectionShow): JsonResponse
    {
        $this->authorize('view', $election);

        $data = $getElectionShow->handle($election, $this->checklistService);

        return $this->success($data);
    }

    public function update(EditRequest $request, Election $election): JsonResponse
    {
        $this->authorize('update', $election);

        $election = $this->service->update($election, $request->validated());

        return $this->success($election, 'Election updated successfully.');
    }

    public function destroy(Election $election): JsonResponse
    {
        $this->authorize('delete', $election);

        $result = $this->service->delete($election);

        if ($result['status']) {
            return $this->fail($result['message'], 422);
        }

        return $this->success(null, 'Election deleted.');
    }

    public function settings(Election $election): JsonResponse
    {
        $this->authorize('view', $election);

        $checklist = $this->checklistService->checklist($election);
        $progress = $this->checklistService->progress($checklist);

        $election->load('setting');

        return $this->success([
            'election' => $election,
            'checklist' => $checklist,
            'progress' => $progress,
        ]);
    }

    public function updateSettings(EditSettingsRequest $request, Election $election): JsonResponse
    {
        $this->authorize('update', $election);

        $setting = $this->settingRepo->update($election, $request->validated());

        return $this->success($setting, 'Settings saved.');
    }
}
