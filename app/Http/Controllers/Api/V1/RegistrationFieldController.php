<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\RegistrationField\CreateRequest;
use App\Models\Election;
use App\Services\Interfaces\RegistrationFieldInterface;
use Illuminate\Http\JsonResponse;

class RegistrationFieldController extends Controller
{
    public function __construct(
        private readonly RegistrationFieldInterface $service
    ) {}

    public function show(Election $election): JsonResponse
    {
        $this->authorize('view', $election->setting);

        return $this->success($election->registrationField);
    }

    public function store(CreateRequest $request, Election $election): JsonResponse
    {
        $this->authorize('update', $election->setting);

        $form = $this->service->create(
            array_merge($request->validated(), [
                'election_id' => $election->id,
            ])
        );

        return $this->success($form, 'Registration form created.', 201);
    }

    public function update(CreateRequest $request, Election $election): JsonResponse
    {
        $this->authorize('update', $election->setting);

        $form = $this->service->update($election->registrationField, $request->validated());

        return $this->success($form, 'Registration form updated.');
    }

    public function destroy(Election $election): JsonResponse
    {
        $this->authorize('update', $election->setting);

        $result = $this->service->delete($election->registrationField);

        if ($result['status']) {
            return $this->fail($result['message'], 422);
        }

        return $this->success(null, $result['message']);
    }
}
