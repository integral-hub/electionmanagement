<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\User;
use App\Enums\PermissionEnum;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_AUDIT_LOGS->value);
    }

    public function view(User $user, ActivityLog $activityLog): bool
    {
        return $user->can(PermissionEnum::VIEW_AUDIT_LOGS->value);
    }

    public function create(User $user): bool
    {
        return false; // logs should never be created manually
    }

    public function update(User $user, ActivityLog $activityLog): bool
    {
        return false; // logs are immutable
    }

    public function delete(User $user, ActivityLog $activityLog): bool
    {
        return false; // optional: lock this completely or add DELETE_AUDIT_LOGS later
    }

    public function restore(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }

    public function forceDelete(User $user, ActivityLog $activityLog): bool
    {
        return false;
    }
}