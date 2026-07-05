<?php

declare(strict_types=1);

namespace App\Actions\Voter\Auth;

use App\Actions\Voter\Auth\Support\VoterOtpSession;
use App\Models\Election;
use App\Models\Voter;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Resolve the pending voter for the requested OTP type (email
 * verification or 2FA) from the session and resend their code.
 */
class ResendVoterOtpAction
{
    use AsAction;

    private const TYPE_VERIFICATION = 'verification';
    private const TYPE_TWO_FACTOR = '2fa';

    /**
     * @return array{sent: bool}
     */
    public function handle(VoterEmailVerificationInterface $service, Election $election, string $type): array
    {
        $isTwoFactor = $type === self::TYPE_TWO_FACTOR;

        $voterId = $isTwoFactor
            ? VoterOtpSession::pendingTwoFactorVoterId()
            : VoterOtpSession::pendingEmailVerifyVoterId();

        $voter = $voterId ? Voter::query()->where('uuid', $voterId)->first() : null;

        if (! $voter) {
            return ['sent' => false];
        }

        $service->send($election, $voter, $isTwoFactor ? 'auth' : null);

        return ['sent' => true];
    }
}