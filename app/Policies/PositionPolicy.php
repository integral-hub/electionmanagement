<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use App\Enums\PermissionEnum;

class PositionPolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_POSITIONS->value);
    }

    /**
     * View position
     */
    public function view(User $user, Position $position): bool
    {
        return $user->can(PermissionEnum::VIEW_POSITIONS->value);
    }

    /**
     * Create position
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_POSITIONS->value);
    }

    /**
     * Update position
     */
    public function update(User $user, Position $position): bool
    {
        return $user->can(PermissionEnum::UPDATE_POSITIONS->value);
    }

    /**
     * Delete position
     */
    public function delete(User $user, Position $position): bool
    {
        return $user->can(PermissionEnum::DELETE_POSITIONS->value);
    }

    /**
     * Restore position
     */
    public function restore(User $user, Position $position): bool
    {
        return false; 
    }

    /**
     * Force delete position
     */
    public function forceDelete(User $user, Position $position): bool
    {
        return false;
    }
}