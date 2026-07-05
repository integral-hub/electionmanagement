<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use App\Enums\PermissionEnum;

class OrganizationPolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_ORGANIZATION->value);
    }

    /**
     * View organization
     */
    public function view(User $user, Organization $organization): bool
    {
        return $user->can(PermissionEnum::VIEW_ORGANIZATION->value);
    }

    /**
     * Create organization
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::UPDATE_ORGANIZATION->value);
    }

    /**
     * Update organization
     */
    public function update(User $user, Organization $organization): bool
    {
        return $user->can(PermissionEnum::UPDATE_ORGANIZATION->value);
    }

    /**
     * Delete organization
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->can(PermissionEnum::DELETE_ORGANIZATION->value);
    }

    /**
     * Restore organization
     */
    public function restore(User $user, Organization $organization): bool
    {
        return false; 
    }

    /**
     * Force delete organization
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }
}