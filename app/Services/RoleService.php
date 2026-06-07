<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\RoleInterface;
use Spatie\Permission\Models\Role;

class RoleService implements RoleInterface
{
    public function create(array $data): Role
    {
        $permissions = $data['permissions'] ?? [];

        unset($data['permissions']);

        $role = Role::query()->create($data);

        if (! empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return $role->refresh();
    }

    public function update(Role $role, array $data): Role
    {
        $permissions = $data['permissions'] ?? null;

        unset($data['permissions']);

        $role->update($data);

        if ($permissions !== null) {
            $role->syncPermissions($permissions);
        }

        return $role->refresh();
    }

    public function delete(Role $role): array|bool
    {
        $result = $this->canDelete($role);

        if ($result['status']) {
            return $result;
        }

        $role->syncPermissions([]);

        $role->delete();

        return [
            'status' => true,
            'message' => 'Role deleted successfully.',
        ];
    }

    private function canDelete(Role $role): array
    {
        $blocked = $role->users()->exists();

        return [
            'status' => $blocked,
            'message' => $blocked
                ? 'Role cannot be deleted because it has assigned users.'
                : null,
        ];
    }
}