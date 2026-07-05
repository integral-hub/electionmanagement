<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Voter\Auth\LoginVoterAction;
use App\Actions\Voter\Auth\RegisterVoterAction;
use App\Actions\Voter\Auth\ResendVoterOtpAction;
use App\Actions\Voter\Auth\Support\VoterOtpSession;
use App\Actions\Voter\Auth\VerifyVoterOtpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VoterLoginRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\Voter\CreateRequest;
use App\Models\Election;
use App\Models\Voter;
use App\Services\Interfaces\Auth\PasswordInterface;
use App\Services\Interfaces\Auth\VoterAuthInterface;
use App\Services\Interfaces\Auth\VoterEmailVerificationInterface;
use App\Services\Interfaces\VoterInterface;
use App\Traits\RFValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoterAuthController extends Controller
{
    use RFValidator;

    public function __construct(
        private readonly VoterAuthInterface $voterAuth,
        private readonly VoterInterface $voterService,
        private readonly VoterEmailVerificationInterface $verifyEmailService,
        private readonly PasswordInterface $passwordService
    ) {}

    public function notReady(Election $election)
    {
        $can = $election->can_vote;

        if ($election->is_portal_ready && $can->allowed) {
            return redirect()->route('voter.login', $election);
        }

        return view('voter.not-ready', compact('election', 'can'));
    }

    // Show login
    public function showLogin(Election $election)
    {
        $loginFields = $this->normalizeLoginFields($election, true);
        $election->load('setting');

        return view('voter.login', compact('election', 'loginFields'));
    }

    // Handle login (step 1)
    public function login(VoterLoginRequest $request, Election $election, LoginVoterAction $loginVoterAction): RedirectResponse
    {
        /** @var Voter $voter */
        $voter = $request->attributes->get('authorized_voter');

        $result = $loginVoterAction->handle($this->voterAuth, $election, $request->validated(), $voter);

        return $result['requiresTwoFactor']
            ? redirect()->route('voter.2fa', $election)
            : redirect()->route('voter.ballot', $election);
    }

    // Link email verification
    public function verifyEmailLink(Election $election, string $token): RedirectResponse
    {
        $verified = $this->verifyEmailService->verify($election, null, null, $token, false);

        if (! $verified) {
            return redirect()
                ->route('voter.login', $election)
                ->with('error', 'Invalid or expired verification link.');
        }

        return redirect()
            ->route('voter.login', $election)
            ->with('success', 'Email verified successfully. You can now log in.');
    }

    // Email verification OTP (after registration)
    public function showVerifyEmail(Election $election)
    {
        if (! VoterOtpSession::hasPendingEmailVerification()) {
            return redirect()->route('voter.login', $election);
        }

        return view('voter.verify-email', compact('election'));
    }

    public function verifyEmailOtp(Request $request, Election $election, VerifyVoterOtpAction $verifyVoterOtp): RedirectResponse
    {
        if (! VoterOtpSession::hasPendingEmailVerification()) {
            return redirect()->route('voter.login', $election);
        }

        $result = $verifyVoterOtp->handle($this->verifyEmailService, $election, (string) $request->input('otp', ''), false);

        if (! $result['success']) {
            return back()->withErrors(['otp' => 'Invalid or expired verification code.']);
        }

        return redirect()
            ->route('voter.login', $election)
            ->with('success', 'Email verified. You can now log in.');
    }

    // 2FA OTP (during login, step 2)
    public function show2fa(Election $election)
    {
        if (! VoterOtpSession::hasPendingTwoFactor()) {
            return redirect()->route('voter.login', $election);
        }

        return view('voter.2fa', compact('election'));
    }

    public function verify2fa(Request $request, Election $election, VerifyVoterOtpAction $verifyVoterOtp): RedirectResponse
    {
        if (! VoterOtpSession::hasPendingTwoFactor()) {
            return redirect()->route('voter.login', $election);
        }

        $result = $verifyVoterOtp->handle($this->verifyEmailService, $election, (string) $request->input('otp', ''), true);

        if (! $result['success']) {
            return back()->withErrors(['otp' => 'Invalid or expired 2FA code.']);
        }

        return redirect()->route('voter.ballot', $election);
    }

    // Resend OTP
    public function resendOtp(Request $request, Election $election, ResendVoterOtpAction $resendVoterOtp): RedirectResponse
    {
        $type = $request->query('type', '2fa'); // 'verification' or '2fa'

        $result = $resendVoterOtp->handle($this->verifyEmailService, $election, $type);

        if (! $result['sent']) {
            return redirect()->route('voter.login', $election);
        }

        return back()->with('success',
            $type === 'verification'
                ? 'A new verification code has been sent to your email.'
                : 'A new login code has been sent to your email.'
        );
    }

    // Registration
    public function showRegister(Election $election)
    {
        if ($guard = $this->guardPortalNotReady($election)) {
            return $guard;
        }

        $election->load(['setting', 'registrationField']);
        $fields = $election->registrationField?->fields ?? [];

        return view('voter.register', compact('election', 'fields'));
    }

    public function register(CreateRequest $request, Election $election, RegisterVoterAction $registerVoter): RedirectResponse
    {
        if ($guard = $this->guardPortalNotReady($election)) {
            return $guard;
        }

        $result = $registerVoter->handle($this->voterService, $election, $request->validated());

        if ($result['needsEmailVerification']) {
            return redirect()
                ->route('voter.verify-email', $election)
                ->with('info', 'Check your email for a 6-digit verification code.');
        }

        return redirect()
            ->route('voter.login', $election)
            ->with('success', 'Registration successful. You can now log in.');
    }

    // Portal guard for the two registration endpoints
    private function guardPortalNotReady(Election $election): ?RedirectResponse
    {
        if ($election->is_portal_ready) {
            return null;
        }

        return redirect()
            ->route('voter.login', $election)
            ->with('info', 'Registration is not available for this election.');
    }

    // Password reset (forgot password, before login)
    public function forgetPassword(Election $election)
    {
        return view('voter.forgot-password', compact('election'));
    }

    public function resetPassword(ForgotPasswordRequest $request, Election $election): RedirectResponse
    {
        $this->passwordService->sendResetLink($request->validated(), 'voters');

        return back()->with('status', 'If that email exists, a reset link has been sent.');
    }

    public function resetView(Election $election, string $token, Request $request)
    {
        return view('voter.reset-password', [
            'election' => $election,
            'token'    => $token,
            'email'    => $request->email,
        ]);
    }

    public function resetStore(Election $election, ResetPasswordRequest $request): RedirectResponse
    {
        $this->passwordService->reset($request->validated(), 'voters');

        return redirect()->route('voter.login', $election)
            ->with('success', 'Password reset. You can now sign in.');
    }

    // Password change (logged-in voter)
    public function editPassword(Election $election)
    {
        return view('voter.password', compact('election'));
    }

    public function updatePassword(Election $election, UpdatePasswordRequest $request): RedirectResponse
    {
        $this->passwordService->update(voter(), $request->validated());

        return redirect()->route('voter.ballot', $election)
            ->with('success', 'Password updated successfully.');
    }

    // Logout
    public function logout(Election $election): RedirectResponse
    {
        $this->voterAuth->logout();

        return redirect()
            ->route('voter.login', $election)
            ->with('success', 'You have been logged out successfully.');
    }
}