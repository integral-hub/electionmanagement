<?php
declare(strict_types=1);
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Services\Interfaces\Auth\PasswordInterface;
use Illuminate\Http\RedirectResponse;

class ForgotPasswordController extends Controller
{
    public function __construct(private readonly PasswordInterface $passwordService) {}

    public function create() 
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $this->passwordService->sendResetLink($request->validated());
        return back()->with('status', 'If that email exists, a reset link has been sent.');
    }
}
