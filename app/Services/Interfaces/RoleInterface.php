<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use Spatie\Permission\Models\Role;

interface RoleInterface
{
    public function create(array $data): Role;
    public function update(Role $role, array $data): Role;
    public function delete(Role $role): array|bool;
}