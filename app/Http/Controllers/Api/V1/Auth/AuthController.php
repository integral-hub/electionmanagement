<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Organization\CreateOrganization;
use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Requests\Organization\CreateRequest;
use App\Services\Interfaces\Auth\LoginInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginInterface $loginService
    ) {}

    /**
     * Register a new organization + its first admin user.
     */
    public function register(CreateRequest $request): JsonResponse
    {
        $organization = CreateOrganization::run($request->payload());

        return $this->success(
            ['organization' => $organization],
            'Organisation registered. You can now log in.',
            201
        );
    }

    /**
     * Authenticate a staff/admin user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->loginService->resolveUser($request->validated());

        $token = $user->createToken('admin-api')->plainTextToken;

        return $this->success([
            'token' => $token,
            'user' => new UserResource(
                $user->load('organization', 'roles', 'permissions')
            ),
        ], 'Logged in successfully.');
    }

    /**
     * Revoke the token used to authenticate the current request.
     */
    public function logout(): JsonResponse
    {
        Auth::user()?->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    /**
     * The currently authenticated admin user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()->load('organization', 'roles', 'permissions')));
    }
}
