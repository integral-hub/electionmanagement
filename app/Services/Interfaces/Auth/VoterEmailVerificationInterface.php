<?php

declare(strict_types=1);

namespace App\Services\Interfaces\Auth;

use App\Models\Election;
use App\Models\Voter;

interface VoterEmailVerificationInterface 
{
    public function send(Election $election, Voter $voter, ?string $context = null): void;

    public function verify(Election $election, ?string $voterId = null, ?string $code = null, ?string $token = null, bool $isAuth = false): bool|array;

    public function matchOtp(Election $election, string $voterId, string $code): bool;

}
