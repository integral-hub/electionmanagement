<?php

namespace App\Policies;

use App\Models\Election;
use App\Models\User;
use App\Enums\PermissionEnum;

class ElectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::VIEW_ELECTIONS->value);
    }

    public function view(User $user, Election $election): bool
    {
        return $user->can(PermissionEnum::VIEW_ELECTIONS->value);
    }

    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::CREATE_ELECTIONS->value);
    }

    public function update(User $user, Election $election): bool
    {
        return $user->can(PermissionEnum::UPDATE_ELECTIONS->value);
    }

    public function delete(User $user, Election $election): bool
    {
        return $user->can(PermissionEnum::DELETE_ELECTIONS->value);
    }

    public function restore(User $user, Election $election): bool
    {
        return false;
    }
    /**
     * Force delete
     */
    public function forceDelete(User $user, Election $election): bool
    {
        return false;
    }
}