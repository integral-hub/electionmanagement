<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permissions seeding
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        // Roles seeding
        foreach (RoleEnum::cases() as $role) {
            Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => 'web',
            ]);
        }

        // Fetch System Admin and set permissions
        $systemAdmin = Role::findByName(
            RoleEnum::System_Admin->value
        );

        $systemAdmin->syncPermissions(
            Permission::all()
        );

        // Fetch Admin and set permissions
        $admin = Role::findByName(
            RoleEnum::Admin->value
        );

        $admin->syncPermissions(
            collect(PermissionEnum::getPermissionFor(RoleEnum::Admin))
                ->map(fn ($permission) => $permission->value)
                ->toArray()
        );
    }
}