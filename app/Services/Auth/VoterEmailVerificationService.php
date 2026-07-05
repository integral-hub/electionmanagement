<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\Election;
use App\Models\Voter;
use App\Notifications\VoterEmailVerification;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tzsk\Otp\Facades\Otp;

class VoterEmailVerificationService implements VoterEmailVerificationInterface
{
    /**
     * Generate and send an OTP (and optionally a verification link).
     */
    public function send(Election $election, Voter $voter, ?string $context = null): void
    {
        $otp  = $context === "import"
            ? null
            : Otp::expiry(5)->generate("email-otp-verification:{$election->uuid}:{$voter->uuid}");
        $link = null;

        if ($context !== "auth") {
            $link = $this->generateLink($voter->uuid, $election);
        }

        $voter->notify(new VoterEmailVerification($election, $otp, $link, $context));
    }

    /**
     * Verify an OTP code or a link with token.
     */
    public function verify(Election $election, ?string $voterId = null, ?string $code = null,
        ?string $token = null, bool $isAuth = false): bool|array {

        // link verification
        if ($token) {
            $voterUuid = Cache::pull("email-verification:{$token}");

            if (! $voterUuid || ! Voter::where('uuid', $voterUuid)->exists()) {
                return false;
            }

            $this->markVerified($voterUuid);
            return true;
        }

        // OTP verification 
        if ($voterId && $code) {
            $valid = Otp::match(
                $code,
                "email-otp-verification:{$election->uuid}:{$voterId}"
            );

            if (! $valid) {
                return false;
            }

            if ($isAuth) {

                /** @var Voter $voter */
                $voter = Voter::query()->where('uuid', $voterId)->first();

                if (! $voter) {
                    return false;
                }

                Auth::guard('voter')->login($voter);

                request()->session()->regenerate();

                $voter->update(['last_login_at' => now()]);

                return [
                    'voter'  => $voter,
                    'status' => 'authenticated',
                ];
            }

            // Registration email-verification OTP — just mark email as verified
            $this->markVerified($voterId);
            return true;
        }

        return false;
    }


    private function markVerified(string $voterUuid): void
    {
        Voter::query()->where('uuid', $voterUuid)->update([
            'is_verified_email' => true,
        ]);
    }
    private function generateLink(string $voter_uuid, Election $election): string
    {
        $token = Str::random(64);
            Cache::put(
                "email-verification:{$token}",
                $voter_uuid,
                now()->addDays(5)
            );
            return route('voter.email.verify', ['election' => $election, 'token' => $token]);

    }
}
