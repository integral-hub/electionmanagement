<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\Election;
use App\Models\Voter;
use App\Services\Interfaces\Auth\VoterAuthInterface;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VoterAuthService implements VoterAuthInterface
{
    public function __construct(
        private readonly VoterEmailVerificationInterface $verifyEmailService
    ) {}

    public function login(Election $election, array $credentials, Voter $voter): array
    {
        // Password check (only when password is one of the login fields)
        $this->checkPassword($voter, $credentials);

        //  2FA logic
        if ($election->setting?->voters_require_2fa) {

            $this->verifyEmailService->send($election, $voter, 'auth');

            return [
                'requires_2fa' => true,
                'voter_uuid'     => $voter->uuid,
            ];
        }

        // Direct login (no 2FA)
        Auth::guard('voter')->login($voter);

        request()->session()->regenerate();

        $voter->update(['last_login_at' => now()]);

        return ['voter' => $voter];
    }

    public function logout(): void
    {
        Auth::guard('voter')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    private function checkPassword(Voter $voter, array $credentials): void
    {
        // Only check if the caller included a 'password' key
        if (! array_key_exists('password', $credentials)) {
            return;
        }

        if (empty($credentials['password'])) {
            throw ValidationException::withMessages([
                'password' => 'Password is required.',
            ]);
        }

        if (! $voter->password) {
            throw ValidationException::withMessages([
                'password' => 'Your account has no password set. Contact the election administrator.',
            ]);
        }

        if (! Hash::check($credentials['password'], $voter->password)) {
            throw ValidationException::withMessages([
                'credentials' => 'Incorrect password.',
            ]);
        }
    }
}
