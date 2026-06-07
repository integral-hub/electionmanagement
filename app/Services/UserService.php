<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use App\Notifications\StaffCreated;
use Illuminate\Support\Str;

class UserService implements UserInterface
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $role = $data['role'] ?? null;
            unset($data['role']);

             $plainPassword = Str::password(12);

            $user = User::query()->create(
                array_merge($data, [
                    'password' => $plainPassword,
                    'organization_id' => $data['organization_id'] ?? Auth::user()?->organization_id,
                ])
            );

            // Assign role after creation
            if ($role) {
                $user->assignRole($role);
            }

            // Notify user
            $user->notify(new StaffCreated($plainPassword));

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        $role = $data['role'] ?? null;

        unset($data['role']);

        $user->update($data);

        if ($role) {
            $user->syncRoles([$role]);
        }

        return $user->refresh();
    }
    public function revokeRole(User $user): array
    {
        $user->syncRoles([]); 
        
        return [
            'status' => true,
            'message' => 'User role revoked successfully.',
        ];
    }
    public function delete(User $user): array
    {
        $user->syncRoles([]); 
        $user->delete();

        return [
            'status' => true,
            'message' => 'User deleted successfully.',
        ];
    }
}