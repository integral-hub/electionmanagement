<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\ValidatePasswordResetToken;

class RedirectIfTokenInvalid
{
    use ValidatePasswordResetToken;

    public function handle(Request $request, Closure $next)
    {
        // user reset
        if ($request->routeIs('password.reset')) {

            if (! $this->isAvailable($request->email, $request->token)) {
                return redirect()->route('login')
                    ->with('error', 'This reset link is invalid or already used.');
            }
        }

        // voter reset
        if ($request->routeIs('voter.password.reset')) {
            $election = $request->route('election');
            if (! $this->isAvailable($request->email, $request->token)) {
                return redirect()->route('voter.login', $election)
                    ->with('error', 'This reset link is invalid or already used.');
            }
        }

        return $next($request);
    }
}