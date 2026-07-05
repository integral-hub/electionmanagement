<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vote;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;

class VotePolicy
{
    /**
     * System Admin only (view all votes / access vote module)
     */
    public function viewAny(User $user): bool
    {
        return $user->getRoleNames()->first() === RoleEnum::System_Admin->value;
    }

    /**
     * View vote records
     */
    public function view(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_VOTES->value);
    }

    /**
     * Casting a vote (if you allow backend enforcement)
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Update vote (rarely allowed, usually disabled)
     */
    public function update(User $user, Vote $vote): bool
    {
        return false;
    }

    /**
     * Delete vote (usually admin-only or disabled completely)
     */
    public function delete(User $user, Vote $vote): bool
    {
        return false;
    }

    /**
     * Reset votes (dangerous admin action)
     */
    public function reset(User $user): bool
    {
        return $user->can(PermissionEnum::RESET_VOTES->value);
    }

    /**
     * Restore votes (if you support recovery)
     */
    public function restore(User $user, Vote $vote): bool
    {
        return $user->can(PermissionEnum::RESTORE_VOTES->value);
    }

    /**
     * Force delete votes (rare, admin only)
     */
    public function forceDelete(User $user, Vote $vote): bool
    {
        return false;
    }
}