<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Position\CreateRequest;
use App\Http\Requests\Position\EditRequest;
use App\Models\Election;
use App\Models\Position;
use App\Services\Interfaces\PositionInterface;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function __construct(
        private readonly PositionInterface $service
    ) {}

    public function store(CreateRequest $request, Election $election): JsonResponse
    {
        $this->authorize('create', Position::class);

        $position = $this->service->create(
            array_merge($request->validated(), [
                'election_id' => $election->id,
            ])
        );

        return $this->success($position, 'Position added.', 201);
    }

    public function update(EditRequest $request, Election $election, Position $position): JsonResponse
    {
        $this->authorize('update', $position);

        $position = $this->service->update($position, $request->validated());

        return $this->success($position, 'Position updated.');
    }

    public function destroy(Election $election, Position $position): JsonResponse
    {
        $this->authorize('delete', $position);

        $result = $this->service->delete($position);

        if (is_array($result) && ($result['status'] ?? false)) {
            return $this->fail($result['message'], 422);
        }

        return $this->success(null, 'Position removed.');
    }
}
