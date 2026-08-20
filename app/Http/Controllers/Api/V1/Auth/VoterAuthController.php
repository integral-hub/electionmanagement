<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VoterLoginRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\Voter\CreateRequest as VoterCreateRequest;
use App\Models\Election;
use App\Models\Voter;
use App\Services\Interfaces\Auth\PasswordInterface;
use App\Services\Interfaces\Auth\VoterAuthInterface;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use App\Services\Interfaces\VoterInterface;
use App\Traits\ResolvesVoterLogin;
use App\Traits\VoteEligibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class VoterAuthController extends Controller
{
    use ResolvesVoterLogin, VoteEligibility;

    public function __construct(
        private readonly VoterInterface $voterService,
        private readonly VoterAuthInterface $voterAuth,
        private readonly VoterEmailVerificationInterface $verifyEmailService,
        private readonly PasswordInterface $passwordService
    ) {}

    public function status(Election $election): JsonResponse
    {
        $election->load('setting');

        return $this->success([
            'is_portal_ready' => $election->is_portal_ready,
            'is_registration_open' => $election->is_registration_open,
            'can_vote' => $election->can_vote,
            'login_fields' => $this->normalizeLoginFields($election, true),
        ]);
    }

    /**
     * Step 1: verify credentials, then either issue a token directly
     * or hand back a challenge for step 2 (2FA).
     */
    public function login(VoterLoginRequest $request, Election $election): JsonResponse
    {
        if (! $election->can_vote->allowed) {
            return $this->fail('Voting is not currently open for this election.', 403);
        }

        $voter = $this->findVoter($election, $request->validated());

        if (! $voter) {
            throw ValidationException::withMessages(['verification' => 'Invalid credentials.']);
        }

        $eligibility = $this->canSignIn($election, $voter);

        if (! $eligibility->allowed) {
            throw ValidationException::withMessages(['credentials' => $eligibility->reason]);
        }

        $this->voterAuth->checkPassword($voter, $request->validated());

        if ($election->setting?->voters_require_2fa) {
            $this->verifyEmailService->send($election, $voter, 'auth');

            return $this->success([
                'requires_two_factor' => true,
                'voter_uuid' => $voter->uuid,
            ], 'A 6-digit login code has been sent to your email.');
        }

        return $this->success(
            ['requires_two_factor' => false, ...$this->authenticate($voter)],
            'Logged in successfully.'
        );
    }

    /**
     * Step 2: verify the 2FA OTP and issue a token.
     */
    public function verifyTwoFactor(Request $request, Election $election): JsonResponse
    {
        $request->validate([
            'voter_uuid' => ['required', 'string'],
            'otp' => ['required', 'string'],
        ]);

        $voter = $this->locateVoter($election, (string) $request->input('voter_uuid'));

        $valid = $voter && $this->verifyEmailService->matchOtp($election, $voter->uuid, (string) $request->input('otp'));

        if (! $valid) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired login code.']);
        }

        return $this->success($this->authenticate($voter), 'Logged in successfully.');
    }

    /**
     * Resend either a registration verification code or a 2FA code.
     */
    public function resendOtp(Request $request, Election $election): JsonResponse
    {
        $request->validate([
            'voter_uuid' => ['required', 'string'],
            'type' => ['required', 'string', 'in:verification,2fa'],
        ]);

        $voter = $this->locateVoter($election, (string) $request->input('voter_uuid'));

        if (! $voter) {
            return $this->fail('Voter not found.', 404);
        }

        $isTwoFactor = $request->input('type') === '2fa';

        $this->verifyEmailService->send($election, $voter, $isTwoFactor ? 'auth' : null);

        return $this->success(null, $isTwoFactor
            ? 'A new login code has been sent to your email.'
            : 'A new verification code has been sent to your email.');
    }

    public function register(VoterCreateRequest $request, Election $election): JsonResponse
    {
        if (! $election->is_registration_open) {
            return $this->fail('Registration is not available for this election.', 403);
        }

        $voter = $this->voterService->create($election, $request->validated());

        $needsEmailVerification = (bool) ($election->setting?->voters_verification_requirement['email'] ?? false);

        return $this->success([
            'voter_uuid' => $voter->uuid,
            'needs_email_verification' => $needsEmailVerification,
        ], $needsEmailVerification
            ? 'Check your email for a 6-digit verification code.'
            : 'Registration successful. You can now log in.', 201);
    }

    public function verifyEmail(Request $request, Election $election): JsonResponse
    {
        $request->validate([
            'voter_uuid' => ['required', 'string'],
            'otp' => ['required', 'string'],
        ]);

        $verified = $this->verifyEmailService->verify(
            $election, (string) $request->input('voter_uuid'), (string) $request->input('otp'), null, false
        );

        if ($verified !== true) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired verification code.']);
        }

        return $this->success(null, 'Email verified. You can now log in.');
    }

    public function verifyEmailLink(Election $election, string $token): JsonResponse
    {
        if (! $this->verifyEmailService->verify($election, null, null, $token, false)) {
            return $this->fail('Invalid or expired verification link.', 422);
        }

        return $this->success(null, 'Email verified successfully. You can now log in.');
    }

    public function forgotPassword(ForgotPasswordRequest $request, Election $election): JsonResponse
    {
        $result = $this->passwordService->sendResetLink($request->validated(), 'voters');

        return $this->success(null, $result['message']);
    }

    public function resetPassword(ResetPasswordRequest $request, Election $election): JsonResponse
    {
        $result = $this->passwordService->reset($request->validated(), 'voters');

        return $this->success(null, $result['message']);
    }

    public function updatePassword(UpdatePasswordRequest $request, Election $election): JsonResponse
    {
        $result = $this->passwordService->update($request->user(), $request->validated());

        return $this->success(null, $result['message']);
    }

    public function me(Election $election): JsonResponse
    {
        return $this->success(request()->user());
    }

    public function logout(Request $request, Election $election): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    /**
     * Shared by resendOtp() and verifyTwoFactor() — both start from a
     * client-supplied voter_uuid and need the same scoped lookup.
     */
    private function locateVoter(Election $election, string $uuid): ?Voter
    {
        return Voter::query()
            ->where('uuid', $uuid)
            ->where('organization_id', $election->organization_id)
            ->first();
    }

    /**
     * Shared by login() (no-2FA path) and verifyTwoFactor() — both end
     * with "voter is now confirmed, hand back a usable session".
     *
     * @return array{token: string, voter: Voter}
     */
    private function authenticate(Voter $voter): array
    {
        $voter->update(['last_login_at' => now()]);

        return [
            'token' => $voter->createToken('voter-api')->plainTextToken,
            'voter' => $voter,
        ];
    }
}
