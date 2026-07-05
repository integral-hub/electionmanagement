<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\PermissionEnum;

class UserPolicy
{
    /**
     * System Admin only (user management access)
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_USERS->value);
    }

    /**
     * View user list / profile
     */
    public function view(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::VIEW_USERS->value);
    }

    /**
     * Create user
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_USERS->value);
    }

    /**
     * Update user
     */
    public function update(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::UPDATE_USERS->value);
    }

    /**
     * Delete user
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can(PermissionEnum::DELETE_USERS->value);
    }

    /**
     * Restore user (optional feature)
     */
    public function restore(User $user, User $model): bool
    {
        return false; 
    }

    /**
     * Force delete user
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}