<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class SetPermissionTeam
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        if($user = $request->user()) {
            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($user->organization_id);
        }

        return $next($request);
    }
}