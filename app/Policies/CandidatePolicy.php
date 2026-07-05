<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;
use App\Enums\PermissionEnum;

class CandidatePolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_CANDIDATES->value);
    }

    /**
     * View single candidate
     */
    public function view(User $user, Candidate $candidate): bool
    {
        return $user->can(PermissionEnum::VIEW_CANDIDATES->value);
    }

    /**
     * Create candidate
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_CANDIDATES->value);
    }

    /**
     * Update candidate
     */
    public function update(User $user, Candidate $candidate): bool
    {
        return $user->can(PermissionEnum::UPDATE_CANDIDATES->value);
    }

    /**
     * Delete candidate
     */
    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->can(PermissionEnum::DELETE_CANDIDATES->value);
    }

    /**
     * Restore candidate (optional)
     */
    public function restore(User $user, Candidate $candidate): bool
    {
        return false;  
    }

    /**
     * Force delete
     */
    public function forceDelete(User $user, Candidate $candidate): bool
    {
        return false;
    }
}