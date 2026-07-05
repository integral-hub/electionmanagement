<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Election;
use App\Models\Voter;
use App\Traits\RFValidator;
use App\Traits\VoteEligibility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeVoterLogin
{
    use VoteEligibility, RFValidator;
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Election $election */
        $election = $request->route('election');

        $voter = $this->findVoter($election, $request->all());

        if (! $voter) {
            throw ValidationException::withMessages([
                'verification' => 'Invalid credentials.',
            ]);
        }

        $check = $this->canSignIn($election, $voter);

        if (! $check->allowed) {
            throw ValidationException::withMessages([
                'credentials' => $check->reason,
            ]);
        }

        $request->attributes->set('authorized_voter', $voter);

        return $next($request);
    }

    private function findVoter(Election $election, array $credentials): ?Voter
    {

        $fields = array_filter(
            $this->normalizeLoginFields($election),
            fn ($field) => $field !== 'password'
        );

        $query = Voter::query()->where('organization_id', $election->organization_id);

        foreach ($fields as $field) {
            $value = $credentials[$field] ?? null;

            if (empty($value)) {
                throw ValidationException::withMessages([
                    $field => ucfirst($field) . ' is required.',
                ]);
            }

            if (in_array($field, ['email', 'phone'], true)) {
                $query->where($field, $value);
            } else {
                $query->whereHas('uniqueData', function ($q) use ($field, $value) {
                    $q->where('field_name', $field)->where('value', $value);
                });
            }
        }

        return $query->first();
    }

}
