<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use App\Enums\PermissionEnum;

class RolePolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_ROLES->value);
    }

    /**
     * View role list / role page
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can(PermissionEnum::VIEW_ROLES->value);
    }

    /**
     * Create role
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_ROLES->value);
    }

    /**
     * Update role
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can(PermissionEnum::UPDATE_ROLES->value);
    }

    /**
     * Delete role
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can(PermissionEnum::DELETE_ROLES->value);
    }
}