<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VotersImportLog;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;

class VotersImportLogPolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->getRoleNames()->first() === RoleEnum::System_Admin->value;
    }

    /**
     * View import log
     */
    public function view(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_IMPORT_LOGS->value);
    }

    /**
     * Create logs (system only)
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Update logs (not allowed - audit logs are immutable)
     */
    public function update(User $user, VotersImportLog $votersImportLog): bool
    {
        return false;
    }

    /**
     * Delete logs 
     */
    public function delete(User $user, VotersImportLog $votersImportLog): bool
    {
        return false;
    }

    /**
     * Restore logs
     */
    public function restore(User $user, VotersImportLog $votersImportLog): bool
    {
        return false;
    }

    /**
     * Force delete logs
     */
    public function forceDelete(User $user, VotersImportLog $votersImportLog): bool
    {
        return false;
    }
}