<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\User\ViewUserAction;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\EditRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Interfaces\UserInterface;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly UserInterface $service
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $users = ViewUserAction::run();

        return $this->success(new UserResource($users));
    }

    public function store(CreateRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = $this->service->create($request->validated());

        return $this->success(new UserResource($user), 'Staff member invited successfully.', 201);
    }

    public function update(EditRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $user = $this->service->update($user, $request->validated());

        return $this->success(new UserResource($user), 'User updated.');
    }

    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete', $user);

        $result = $this->service->delete($user);

        return $this->success(null, $result['message']);
    }
}
