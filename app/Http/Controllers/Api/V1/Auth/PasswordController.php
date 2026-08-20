<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Services\Interfaces\Auth\PasswordInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function __construct(
        private readonly PasswordInterface $passwordService
    ) {}

    /**
     * Request a password reset link for an admin/staff account.
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordService->sendResetLink($request->validated());

        return $this->success(null, $result['message']);
    }

    /**
     * Reset an admin/staff password using the emailed token.
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->passwordService->reset($request->validated());

        return $this->success(null, $result['message']);
    }

    /**
     * Change password for the currently authenticated admin/staff user.
     */
    public function update(UpdatePasswordRequest $request): JsonResponse
    {
        $result = $this->passwordService->update($request->user(), $request->validated());

        return $this->success(null, $result['message']);
    }
}
