<?php
declare(strict_types=1);
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Interfaces\Auth\PasswordInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function __construct(private readonly PasswordInterface $passwordService) {}

    public function create(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $this->passwordService->reset($request->validated());
        return redirect()->route('login')->with('success', 'Password reset. You can now sign in.');
    }
}
