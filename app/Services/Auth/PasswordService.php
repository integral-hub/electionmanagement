<?php
declare(strict_types=1);

namespace App\Services\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\Auth\PasswordInterface;
use Illuminate\Database\Eloquent\Model;

class PasswordService implements PasswordInterface
{
    /**
     * Send reset link (users or voters)
     */
    public function sendResetLink(array $data, string $broker = 'users'): array
    {
        $status = Password::broker($broker)->sendResetLink([
            'email' => $data['email'],
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return [
            'status' => true,
            'message' => __($status),
        ];
    }

    /**
     * Reset password (users or voters)
     */
    public function reset(array $data, string $broker = 'users'): array
    {
        $status = Password::broker($broker)->reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return [
            'status' => true,
            'message' => __($status),
        ];
    }

    /**
     * Change password for authenticated user/voter
     */
    public function update(Model $guard, array $data): array
    {
        if (! Hash::check($data['current_password'], $guard->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }
        $guard->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();

        return [
            'status' => true,
            'message' => 'Password updated successfully.',
        ];
    }
}