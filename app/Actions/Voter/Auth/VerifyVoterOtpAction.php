<?php

declare(strict_types=1);

namespace App\Actions\Voter\Auth;

use App\Actions\Voter\Auth\Support\VoterOtpSession;
use App\Models\Election;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Verify a 6-digit OTP for either the post-registration email
 * verification flow or the login 2FA flow.
 */
class VerifyVoterOtpAction
{
    use AsAction;

    /**
     * @return array{success: bool, reason?: string}
     */
    public function handle(VoterEmailVerificationInterface $service, Election $election, string $otp, bool $isTwoFactor): array
    {
        $pending = VoterOtpSession::pendingFor($isTwoFactor);

        if (! $pending['voterId']) {
            return ['success' => false, 'reason' => 'expired'];
        }

        $result = $service->verify($election, $pending['voterId'], $otp, null, $isTwoFactor);

        if (! $this->wasSuccessful($result, $isTwoFactor)) {
            return ['success' => false, 'reason' => 'invalid'];
        }

        VoterOtpSession::clearFor($isTwoFactor);

        return ['success' => true];
    }

    private function wasSuccessful(mixed $result, bool $isTwoFactor): bool
    {
        if (! $isTwoFactor) {
            return $result === true;
        }

        return is_array($result) && ($result['status'] ?? null) === 'authenticated';
    }
}