<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registered as 'voter.guest' in bootstrap/app.php (or Kernel.php).
 *
 * Redirects an already-authenticated voter away from login/register screens
 * back to the ballot page for their election.
 */
class RedirectIfVoterAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('voter')->check()) {
            $election = $request->route('election');

            if ($election) {
                return redirect()->route('voter.ballot', $election);
            }
        }

        return $next($request);
    }
}
