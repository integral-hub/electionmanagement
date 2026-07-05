<?php

namespace Database\Seeders;


use App\Models\Organization;
use App\Models\User;
use App\Actions\Organization\CreateOrganization;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ORGANIZATION + ADMIN
        $organization = Organization::factory()->create();

        $orgAdmin = User::factory()
            ->organizationStaff($organization->id)
            ->create([
                'email' => 'admin@organization.com',
                'name'  => 'Organization Admin',
            ]);

        $adminRole = app(CreateOrganization::class)
                        ->createAdminRoleWithPermissions($organization);
        
        setPermissionsTeamId($organization->id);
        // ASSIGN ROLE TO USER
        $orgAdmin->assignRole($adminRole);

        // ORGANIZATION STAFF
        User::factory()
            ->count(2)
            ->organizationStaff($organization->id)
            ->create();

    }
}