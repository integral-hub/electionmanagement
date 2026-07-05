<?php

declare(strict_types=1);

namespace App\Actions\Voter\Auth\Support;

use App\Models\Election;

/**
 * Reads/writes the session state used while a voter has a pending
 * OTP verification, either post-registration email verification or
 * login 2FA.
 */
final class VoterOtpSession
{
    private const TWO_FACTOR_VOTER_ID = 'voter_2fa_id';
    private const TWO_FACTOR_ELECTION_ID = 'voter_2fa_election';
    private const EMAIL_VERIFY_VOTER_ID = 'voter_verify_id';

    public static function startTwoFactor(Election $election, string $voterUuid): void
    {
        session([
            self::TWO_FACTOR_VOTER_ID => $voterUuid,
            self::TWO_FACTOR_ELECTION_ID => $election->id,
        ]);
    }

    public static function startEmailVerification(string $voterUuid): void
    {
        session([self::EMAIL_VERIFY_VOTER_ID => $voterUuid]);
    }

    public static function pendingTwoFactorVoterId(): ?string
    {
        return session(self::TWO_FACTOR_VOTER_ID);
    }

    public static function pendingEmailVerifyVoterId(): ?string
    {
        return session(self::EMAIL_VERIFY_VOTER_ID);
    }

    public static function hasPendingTwoFactor(): bool
    {
        return (bool) self::pendingTwoFactorVoterId();
    }

    public static function hasPendingEmailVerification(): bool
    {
        return (bool) self::pendingEmailVerifyVoterId();
    }

    public static function clearTwoFactor(): void
    {
        session()->forget([self::TWO_FACTOR_VOTER_ID, self::TWO_FACTOR_ELECTION_ID]);
    }

    public static function clearEmailVerification(): void
    {
        session()->forget(self::EMAIL_VERIFY_VOTER_ID);
    }

    /** @return array{voterId: ?string, sessionKey: string} */
    public static function pendingFor(bool $isTwoFactor): array
    {
        return $isTwoFactor
            ? ['voterId' => self::pendingTwoFactorVoterId(), 'sessionKey' => self::TWO_FACTOR_VOTER_ID]
            : ['voterId' => self::pendingEmailVerifyVoterId(), 'sessionKey' => self::EMAIL_VERIFY_VOTER_ID];
    }

    public static function clearFor(bool $isTwoFactor): void
    {
        $isTwoFactor ? self::clearTwoFactor() : self::clearEmailVerification();
    }
}