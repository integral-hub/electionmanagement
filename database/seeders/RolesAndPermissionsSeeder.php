<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
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
            Role::firstOrCreate([
                'name' => RoleEnum::System_Admin->value,
                'guard_name' => 'web',
            ]);

        // Fetch System Admin and set permissions
        $systemAdmin = Role::findByName(
            RoleEnum::System_Admin->value
        );

        $systemAdmin->syncPermissions(
            Permission::all()
        );

    }
}