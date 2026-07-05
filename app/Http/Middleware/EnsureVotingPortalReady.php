<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Registered as 'voter.auth'
 */
class EnsureVotingPortalReady
{
    public function handle(Request $request, Closure $next): Response
    {
        
        $election = $request->route('election');

        if ($election && ! $election->is_portal_ready) {
            return redirect()->route('voter.not-ready', $election);
        }

        return $next($request);
    }
}
