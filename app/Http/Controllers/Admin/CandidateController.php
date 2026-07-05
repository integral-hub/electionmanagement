<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\CandidateStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Candidate\CreateRequest;
use App\Http\Requests\Candidate\EditRequest;
use App\Models\Candidate;
use App\Models\Election;
use App\Services\Interfaces\CandidateInterface;
use Illuminate\Http\RedirectResponse;

class CandidateController extends Controller
{
    public function __construct(
        private readonly CandidateInterface $service
    ) {}

    public function create(Election $election)
    {
        $this->authorize('create', Candidate::class);

        $positions = $election->positions()->get();
        $statuses = CandidateStatusEnum::values();

        return view('admin.candidates.create', compact('election', 'positions', 'statuses'));
    }

    public function store(CreateRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('create', Candidate::class);

        $data = $request->validated();
        $data['election_id'] = $election->id;

        $this->service->create($data);

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Candidate added.');
    }

    public function edit(Election $election, Candidate $candidate)
    {
        $this->authorize('update', $candidate);

        $positions = $election->positions()->get();
        $statuses = CandidateStatusEnum::values();

        return view('admin.candidates.create', compact(
            'election',
            'candidate',
            'positions',
            'statuses'
        ));
    }

    public function update(EditRequest $request, Election $election, Candidate $candidate): RedirectResponse 
    {
        $this->authorize('update', $candidate);

        $data = $request->validated();

        $this->service->update($candidate, $data);

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Candidate updated.');
    }

    public function destroy(Election $election, Candidate $candidate): RedirectResponse 
    {
        $this->authorize('delete', $candidate);

        $result = $this->service->delete($candidate);

        if (is_array($result) && $result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Candidate removed.');
    }
}