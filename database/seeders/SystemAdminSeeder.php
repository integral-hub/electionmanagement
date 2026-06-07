<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        $systemAdmin = User::query()
            ->where('email', config('settings.system_admin.email'))
            ->first();

        if (!$systemAdmin) {
            $systemAdmin = User::factory()
                ->systemAdmin()
                ->create();
        }

        $systemAdmin->assignRole(
            RoleEnum::System_Admin->value
        );
    }
}