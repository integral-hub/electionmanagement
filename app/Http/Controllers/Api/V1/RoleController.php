<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\PermissionEnum;
use App\Http\Requests\Role\RoleRequest;
use App\Models\Role;
use App\Services\Interfaces\RoleInterface;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleInterface $service
    ) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Role::class);

        $roles = $this->service->getRoles(true);

        return $this->success([
            'roles' => $roles,
            'permissions' => PermissionEnum::values(),
        ]);
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $this->authorize('create', Role::class);

        $role = $this->service->create($request->validatedPayload());

        return $this->success($role, 'Role created.', 201);
    }

    public function update(RoleRequest $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);

        $role = $this->service->update($role, $request->validatedPayload());

        return $this->success($role, 'Role updated.');
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);

        $result = $this->service->delete($role);

        if (is_array($result) && ($result['status'] ?? false)) {
            return $this->fail($result['message'], 422);
        }

        return $this->success(null, 'Role deleted.');
    }
}
