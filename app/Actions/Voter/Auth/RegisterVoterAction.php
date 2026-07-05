<?php

declare(strict_types=1);

namespace App\Actions\Voter\Auth;

use App\Actions\Voter\Auth\Support\VoterOtpSession;
use App\Models\Election;
use App\Models\Voter;
use App\Services\Interfaces\VoterInterface;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Create a voter for the election, and check if verification is needed
 */
class RegisterVoterAction
{
    use AsAction;

    /**
     * @return array{voter: Voter, needsEmailVerification: bool}
     */
    public function handle(VoterInterface $voterService, Election $election, array $data): array
    {
        $voter = $voterService->create($election, $data);

        $needsEmailVerification = (bool) $election->setting?->voters_verification_requirement['email'] ?? false;
        
        if ($needsEmailVerification) {
            VoterOtpSession::startEmailVerification($voter->uuid);
        }

        return [
            'voter'                  => $voter,
            'needsEmailVerification' => $needsEmailVerification,
        ];
    }

}