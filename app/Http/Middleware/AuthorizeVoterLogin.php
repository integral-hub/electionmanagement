<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Election;
use App\Traits\ResolvesVoterLogin;
use App\Traits\VoteEligibility;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeVoterLogin
{
    use VoteEligibility, ResolvesVoterLogin;

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
}
