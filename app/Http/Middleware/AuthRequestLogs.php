<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthRequestLogs
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        app()->instance('global', [
            'org_id' => $request->user()?->organization_id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $next($request);
    }
}
