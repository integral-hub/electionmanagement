<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Forces unauthenticated visitors to the voter login page 
 * for the specific election they are trying to access.
 */
class EnsureVoterIsAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('voter')->check()) {
            $election = $request->route('election');

            if ($election) {
                return redirect()
                    ->route('voter.login', $election)
                    ->with('info', 'Please sign in to access the ballot.');
            }

            return redirect()->route('login');
        }
        app()->instance('voter', Auth::guard('voter')->user());

        return $next($request);
    }
}
