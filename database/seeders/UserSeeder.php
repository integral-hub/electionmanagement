<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\User;
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

        $orgAdmin->assignRole(
            RoleEnum::Admin->value
        );

        // ORGANIZATION STAFF
        User::factory()
            ->count(2)
            ->organizationStaff($organization->id)
            ->create();

    }
}