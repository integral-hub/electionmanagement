<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Models\Voter;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the bearer token used on a voter-portal API route belongs
 * to a Voter, not a staff User. Registered as 'voter.token'.
 */
class EnsureVoterTokenAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $voter = $request->user();

        if (! $voter instanceof Voter) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        app()->instance('voter', $voter);

        return $next($request);
    }
}
