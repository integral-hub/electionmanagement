<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Services\Interfaces\RoleInterface;
use App\Enums\PermissionEnum;
use App\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleInterface $service
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = $this->service->getRoles(true);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $this->authorize('create', Role::class);

        $permissions = PermissionEnum::values();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $this->service->create($request->validatedPayload());

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created.');
    }

    public function edit(Role $role)
    {
        $this->authorize('update', $role);

        $role->load('permissions');
        $permissions = PermissionEnum::values();

        return view('admin.roles.create', compact('role', 'permissions'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $this->authorize('update', $role);

        $this->service->update($role, $request->validatedPayload());

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $result = $this->service->delete($role);

        if (is_array($result) && $result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted.');
    }
}