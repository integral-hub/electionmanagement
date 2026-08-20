<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\User\UpdateRequest;
use App\Services\Interfaces\UserInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserInterface $userService
    ) {}

    public function show(): JsonResponse
    {
        return $this->success(Auth::user()->load('organization', 'roles'));
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $user = $this->userService->updateProfile(Auth::user(), $request->validated());

        return $this->success($user, 'Profile updated.');
    }
}
