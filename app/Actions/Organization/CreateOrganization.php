<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Enums\RoleEnum;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrganizationCreated;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateOrganization
{
    use AsAction;

    public function handle(array $data): Organization
    {
        return DB::transaction(function () use ($data) {

            $organization = Organization::query()->create(
                $data['organization']
            );

            $user = User::query()->create([
                ...$data['user'],
                'organization_id' => $organization->id,
            ]);

            $user->assignRole(
                RoleEnum::Admin->value
            );

            DB::table('organization_tokens')
                ->where('token', $data['organization']['access_token'])
                ->update([
                    'organization_id' => $organization->id,
                    'is_used' => true,
                ]);

                // Notify user
            $user->notify(
                new OrganizationCreated(
                    organization: $organization,
                    role: RoleEnum::Admin->value
                )
            );

            return $organization->load('users');
        });
    }
}