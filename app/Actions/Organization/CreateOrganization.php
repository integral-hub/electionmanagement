<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Enums\RoleEnum;
use App\Enums\PermissionEnum;
use App\Models\Organization;
use App\Models\OrganizationToken;
use App\Models\User;
use App\Notifications\OrganizationCreated;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use App\Models\Role;

class CreateOrganization
{
    use AsAction;

    public function handle(array $data): Organization
    {
        return DB::transaction(function () use ($data) {

            $org_data = $data['organization'];

            $token = OrganizationToken::query()
                ->where('token', $org_data['access_token'])
                ->firstOrFail();

            $org_data['package_type'] = $token->name;
            unset($org_data['access_token']);

            $organization = Organization::query()->create($org_data);

            $user = User::query()->create([
                ...$data['user'],
                'organization_id' => $organization->id,
            ]);

            $role = $this->createAdminRoleWithPermissions($organization);
            setPermissionsTeamId($organization->id);
            $user->assignRole($role);

            $token->update([
                'organization_id' => $organization->id,
                'is_used' => true,
            ]);

            $user->notify(
                new OrganizationCreated(
                    organization: $organization,
                    role: RoleEnum::Admin->value
                )
            );

            return $organization->load('users');
        });
    }

    /**
     * Create role + attach default permissions for organization admin
     */
    public function createAdminRoleWithPermissions(Organization $organization): Role
    {
        $role = Role::create([
            'organization_id' => $organization->id,
            'name' => RoleEnum::Admin->value,
            'guard_name' => 'web',
        ]);

        $permissions = PermissionEnum::getPermissionFor(RoleEnum::Admin);

        $role->syncPermissions(
            collect($permissions)
                ->map(fn ($permission) => $permission->value)
                ->toArray()
        );

        return $role;
    }
}