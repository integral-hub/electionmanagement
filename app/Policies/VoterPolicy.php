<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voter;
use App\Enums\PermissionEnum;

class VoterPolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_VOTERS->value);
    }

    /**
     * View voter
     */
    public function view(User $user, Voter $voter): bool
    {
        return $user->can(PermissionEnum::VIEW_VOTERS->value);
    }

    /**
     * Create voter (import-based system)
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::IMPORT_VOTERS->value);
    }

    /**
     * Update voter
     */
    public function update(User $user, Voter $voter): bool
    {
        return $user->can(PermissionEnum::UPDATE_VOTERS->value);
    }

    /**
     * Delete voter
     */
    public function delete(User $user, Voter $voter): bool
    {
        return $user->can(PermissionEnum::DELETE_VOTERS->value);
    }

    /**
     * Approve voter
     */
    public function approve(User $user, Voter $voter): bool
    {
        return $user->can(PermissionEnum::APPROVE_VOTERS->value);
    }

    /**
     * Reject voter
     */
    public function reject(User $user, Voter $voter): bool
    {
        return $user->can(PermissionEnum::REJECT_VOTERS->value);
    }

    /**
     * Import voters
     */
    public function import(User $user): bool
    {
        return $user->can(PermissionEnum::IMPORT_VOTERS->value);
    }
    /**
     * View Import voters
     */
    public function viewImport(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_IMPORT_LOGS->value);
    }

    /**
     * Assign voters
     */
    public function assign(User $user): bool
    {
        return $user->can(PermissionEnum::ASSIGN_VOTERS->value);
    }

    /**
     * Restore voter
     */
    public function restore(User $user, Voter $voter): bool
    {
        return false;
    }

    /**
     * Force delete voter
     */
    public function forceDelete(User $user, Voter $voter): bool
    {
        return false;
    }
}