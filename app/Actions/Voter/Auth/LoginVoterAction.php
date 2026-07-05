<?php

declare(strict_types=1);

namespace App\Actions\Voter\Auth;

use App\Actions\Voter\Auth\Support\VoterOtpSession;
use App\Models\Election;
use App\Models\Voter;
use App\Services\Interfaces\Auth\VoterAuthInterface;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Authenticate a voter
 */
class LoginVoterAction
{
    use AsAction;

    /**
     * @return array{requiresTwoFactor: bool}
     */
    public function handle(VoterAuthInterface $voterAuth, Election $election, array $credentials, Voter $voter): array
    {
        $result = $voterAuth->login($election, $credentials, $voter);

        if (empty($result['requires_2fa'])) {
            return ['requiresTwoFactor' => false];
        }

        VoterOtpSession::startTwoFactor($election, $result['voter_uuid']);

        return ['requiresTwoFactor' => true];
    }
}