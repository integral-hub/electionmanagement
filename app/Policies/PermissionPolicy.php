<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    
    // CRUD actions
    public function view(User $user, string $resource): bool
    {
        return $user->can("view.$resource");
    }

    public function create(User $user, string $resource): bool
    {
        return $user->can("create.$resource");
    }

    public function update(User $user, string $resource): bool
    {
        return $user->can("update.$resource");
    }

    public function delete(User $user, string $resource): bool
    {
        return $user->can("delete.$resource");
    }

    // Special actions
    public function approve(User $user, string $resource): bool
    {
        return $user->can("approve.$resource");
    }

    public function reject(User $user, string $resource): bool
    {
        return $user->can("reject.$resource");
    }

    public function import(User $user, string $resource): bool
    {
        return $user->can("import.$resource");
    }

    public function export(User $user, string $resource): bool
    {
        return $user->can("export.$resource");
    }

    public function reset(User $user, string $resource): bool
    {
        return $user->can("reset.$resource");
    }
}