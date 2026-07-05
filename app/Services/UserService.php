<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Services\Interfaces\UserInterface;
use App\Notifications\StaffCreated;
use App\Services\Interfaces\FileUploadInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService implements UserInterface
{
    public function __construct(
        private readonly FileUploadInterface $fileService
    ) {}

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {

            $role = $data['role'] ?? null;
            unset($data['role']);

             $plainPassword = Str::password(12);

            $user = User::query()->create(
                array_merge($data, [
                    'password' => $plainPassword,
                    'organization_id' => global_data('org_id'),
                ])
            );
            Log::info('Cred', [
                'email' => $user->email,
                'value' => $plainPassword]);

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

    public function updateProfile(User $user, array $data): User
    {
        if (isset($data['profile_photo']) && $data['profile_photo'] instanceof UploadedFile) {
            // Delete existing profile photo
            if (!empty($user->profile_photo_path['public_id'])) {
                $this->fileService->delete($user->profile_photo_path['public_id'], 'image');
            }

            $upload = $this->fileService->upload($data['profile_photo'], 'admin-photo');

            $data['profile_photo_path'] = $upload;
        }

        $user->update($data);

        return $user->refresh();
    } 
}