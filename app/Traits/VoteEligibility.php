<?php

declare(strict_types=1);

namespace App\Traits;

use App\DTO\EligibilityResult;
use App\Models\Election;
use App\Models\Voter;

/**
 * Methods to decide whether a voter may sign-in
 * Cast a vote for a given election.
 */
trait VoteEligibility
{
    public function portalReady(Election $election): bool
    {
        $setting = $election->setting;

        if (! $setting) return false;
        if (! $setting->voting_start) return false;
        if (! $election->positions()->exists()) return false;
        if (! $election->candidates()->exists()) return false;
        if ($election->candidates()->count() < 2) return false;

        if ($setting->registration_mode === 'open' && ! $election->registrationField()->exists()) return false;
        if ($setting->registration_mode === 'closed' && ! $election->voters()->exists()) return false;

        return true;
    }

    public function registrationOpen(Election $election): bool
    {
        return $this->portalReady($election)
            && $election->setting?->registration_mode === 'open';
    }

    public function canSignIn(Election $election, Voter $voter): EligibilityResult
    {
        $pivot = $voter->elections()
            ->where('election_id', $election->id)
            ->first()?->pivot;

        if (! $pivot) {
            return new EligibilityResult(false, 'You are not registered for this election.');
        }

        if ($pivot->status === 'banned') {
            return new EligibilityResult(false, 'Your access to this election has been revoked.');
        }

        if (! $voter->is_verified_email) {
            return new EligibilityResult(false, 'Please verify your email address before signing in.');
        }

        $setting = $election->setting;

        if (! $setting?->vote_before_validation) {
            if ($pivot->status === 'pending') {
                return new EligibilityResult(false, 'Your registration is pending approval.');
            }
        }

        return new EligibilityResult(true);
    }
    
    public function canVote(Election $election): EligibilityResult
    {
        if ($election->status !== 'running') {
            return new EligibilityResult(false, 'Voting is not currently open.');
        }

        $setting = $election->setting;
        $now = now();

        if ($setting?->voting_start && $now->lt($setting->voting_start)) {
            return new EligibilityResult(false, 'Voting has not started yet.');
        }

        if ($setting?->voting_end && $now->gt($setting->voting_end)) {
            return new EligibilityResult(false, 'Voting has ended.');
        }

        return new EligibilityResult(true);
    }

}