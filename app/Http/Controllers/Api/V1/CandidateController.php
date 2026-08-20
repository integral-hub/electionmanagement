<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Candidate\CreateRequest;
use App\Http\Requests\Candidate\EditRequest;
use App\Models\Candidate;
use App\Models\Election;
use App\Services\Interfaces\CandidateInterface;
use Illuminate\Http\JsonResponse;

class CandidateController extends Controller
{
    public function __construct(
        private readonly CandidateInterface $service
    ) {}

    public function store(CreateRequest $request, Election $election): JsonResponse
    {
        $this->authorize('create', Candidate::class);

        $data = $request->validated();
        $data['election_id'] = $election->id;

        $candidate = $this->service->create($data);

        return $this->success($candidate, 'Candidate added.', 201);
    }

    public function update(EditRequest $request, Election $election, Candidate $candidate): JsonResponse
    {
        $this->authorize('update', $candidate);

        $candidate = $this->service->update($candidate, $request->validated());

        return $this->success($candidate, 'Candidate updated.');
    }

    public function destroy(Election $election, Candidate $candidate): JsonResponse
    {
        $this->authorize('delete', $candidate);

        $result = $this->service->delete($candidate);

        if (is_array($result) && ($result['status'] ?? false)) {
            return $this->fail($result['message'], 422);
        }

        return $this->success(null, 'Candidate removed.');
    }
}
