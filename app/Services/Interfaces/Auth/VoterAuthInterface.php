<?php
declare(strict_types=1);
namespace App\Services\Interfaces\Auth;
use App\Models\Election;
use App\Models\Voter;

interface VoterAuthInterface
{
    public function login(Election $election, array $credentials, Voter $voter): array;

    public function checkPassword(Voter $voter, array $credentials): void;

    public function logout(): void;
}
