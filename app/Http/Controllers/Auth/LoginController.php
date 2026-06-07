<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Interfaces\Auth\LoginInterface;

class LoginController extends Controller
{
    public function __construct(
        private readonly LoginInterface $service
    ) {}

    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse {

        $this->service->login($request->validated());

        return redirect()->intended(
            route('dashboard')
        );
    }

    public function destroy(): RedirectResponse
    {
        $this->service->logout();

        return redirect()->route('login');
    }
}