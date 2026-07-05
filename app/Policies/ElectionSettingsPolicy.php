<?php

namespace App\Policies;

use App\Models\ElectionSetting;
use App\Models\User;
use App\Enums\PermissionEnum;

class ElectionSettingsPolicy
{
    /**
     * System Admin only
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_ELECTION_SETTINGS->value);
    }

    /**
     * View settings
     */
    public function view(User $user, ElectionSetting $electionSetting): bool
    {
        return $user->can(PermissionEnum::VIEW_ELECTION_SETTINGS->value);
    }

    /**
     * Create settings
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::UPDATE_ELECTION_SETTINGS->value);
    }

    /**
     * Update settings
     */
    public function update(User $user, ElectionSetting $electionSetting): bool
    {
        return $user->can(PermissionEnum::UPDATE_ELECTION_SETTINGS->value);
    }

    /**
     * Delete settings 
     */
    public function delete(User $user, ElectionSetting $electionSetting): bool
    {
        return false; 
    }

    public function restore(User $user, ElectionSetting $electionSetting): bool
    {
        return false;
    }

    public function forceDelete(User $user, ElectionSetting $electionSetting): bool
    {
        return false;
    }
}