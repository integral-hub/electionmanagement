<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationTokenSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $tokens = [
            // Starter (1 election)
            ['token' => 'STARTER-001', 'max_elections' => 1],
            ['token' => 'STARTER-002', 'max_elections' => 1],
            ['token' => 'STARTER-003', 'max_elections' => 1],
            ['token' => 'STARTER-004', 'max_elections' => 1],
            ['token' => 'STARTER-005', 'max_elections' => 1],

            // Pro (2 elections)
            ['token' => 'PRO-001', 'max_elections' => 2],
            ['token' => 'PRO-002', 'max_elections' => 2],
            ['token' => 'PRO-003', 'max_elections' => 2],
            ['token' => 'PRO-004', 'max_elections' => 2],
            ['token' => 'PRO-005', 'max_elections' => 2],

            // Enterprise (3 elections)
            ['token' => 'ENTERPRISE-001', 'max_elections' => 3],
            ['token' => 'ENTERPRISE-002', 'max_elections' => 3],
            ['token' => 'ENTERPRISE-003', 'max_elections' => 3],
            ['token' => 'ENTERPRISE-004', 'max_elections' => 3],
            ['token' => 'ENTERPRISE-005', 'max_elections' => 3],
        ];

        $tokens = array_map(function ($token) use ($now) {
            return [
                'organization_id' => null,
                'token' => $token['token'],
                'is_used' => false,
                'max_elections' => $token['max_elections'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $tokens);

        DB::table('organization_tokens')->insert($tokens);
    }
}