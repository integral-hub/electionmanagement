<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\Auth\LoginInterface;

class LoginService implements LoginInterface
{
    /**
     * Validate credentials and return the user
     */
    public function resolveUser(array $credentials): User
    {
        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Invalid login.',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => 'Account is inactive.',
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        return $user;
    }

    public function login(array $credentials): User
    {
        $user = $this->resolveUser($credentials);

        Auth::login($user, $credentials['remember'] ?? false);

        request()->session()->regenerate();

        return Auth::user();
    }

    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();
    }
}
