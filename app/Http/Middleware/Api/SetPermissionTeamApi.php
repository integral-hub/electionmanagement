<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

/**
 * API equivalent of App\Http\Middleware\SetPermissionTeam.
 * Returns a JSON 401 instead of redirecting, and only applies
 * the permission team when the authenticated principal is a User
 * (staff/admin token), not a Voter token.
 */
class SetPermissionTeamApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user = $request->user();

        if ($user && isset($user->organization_id)) {
            app(PermissionRegistrar::class)
                ->setPermissionsTeamId($user->organization_id);
        }

        return $next($request);
    }
}
