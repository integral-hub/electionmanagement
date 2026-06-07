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
            ['name' => 'STARTER', 'token' => 'STARTER-TOKEN-001', 'max_elections' => 1],
            ['name' => 'STARTER', 'token' => 'STARTER-TOKEN-002', 'max_elections' => 1],
            ['name' => 'STARTER', 'token' => 'STARTER-TOKEN-003', 'max_elections' => 1],
            ['name' => 'STARTER', 'token' => 'STARTER-TOKEN-004', 'max_elections' => 1],
            ['name' => 'STARTER', 'token' => 'STARTER-TOKEN-005', 'max_elections' => 1],

            ['name' => 'PRO', 'token' => 'PRO-TOKEN-001', 'max_elections' => 2],
            ['name' => 'PRO', 'token' => 'PRO-TOKEN-002', 'max_elections' => 2],
            ['name' => 'PRO', 'token' => 'PRO-TOKEN-003', 'max_elections' => 2],
            ['name' => 'PRO', 'token' => 'PRO-TOKEN-004', 'max_elections' => 2],
            ['name' => 'PRO', 'token' => 'PRO-TOKEN-005', 'max_elections' => 2],

            ['name' => 'ENTERPRISE', 'token' => 'ENT-TOKEN-001', 'max_elections' => 3],
            ['name' => 'ENTERPRISE', 'token' => 'ENT-TOKEN-002', 'max_elections' => 3],
            ['name' => 'ENTERPRISE', 'token' => 'ENT-TOKEN-003', 'max_elections' => 3],
            ['name' => 'ENTERPRISE', 'token' => 'ENT-TOKEN-004', 'max_elections' => 3],
            ['name' => 'ENTERPRISE', 'token' => 'ENT-TOKEN-005', 'max_elections' => 3],
];

        $tokens = array_map(function ($token) use ($now) {
            return [
                'organization_id' => null,
                'name' => $token['name'],
                'token' => $token['token'], // or 'token' => strtoupper(bin2hex(random_bytes(16))),
                'is_used' => false,
                'max_elections' => $token['max_elections'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $tokens);

        DB::table('organization_tokens')->insert($tokens);
    }
}