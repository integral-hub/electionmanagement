<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Services\Interfaces\Auth\LoginInterface;

class LoginService implements LoginInterface
{
    public function login(array $credentials): User
    {
        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => 'Account is inactive.',
            ]);
        }

        if (! Auth::attempt($credentials))
        {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

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