<?php
declare(strict_types=1);
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Services\Interfaces\Auth\PasswordInterface;
use App\Services\Interfaces\UserInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly PasswordInterface $passwordService,
        private readonly UserInterface $userService
    ) {}

    public function show()
    {
        $user = Auth::user()->load('organization', 'roles');
        return view('admin.profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateRequest $request): RedirectResponse
    {
        $this->userService->updateProfile(Auth::user(), $request->validated());

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated.');
    }

    public function editPassword()
    {
        return view('admin.profile.password');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {   
        $this->passwordService->update(Auth::user(), $request->validated());
        return redirect()->route('admin.profile.show')->with('success', 'Password changed successfully.');
    }
}
