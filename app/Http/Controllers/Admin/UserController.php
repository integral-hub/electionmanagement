<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\User\ViewUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\EditRequest;
use App\Models\User;
use App\Services\Interfaces\UserInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\Interfaces\RoleInterface;

class UserController extends Controller
{
    public function __construct(
        private readonly UserInterface $service,
        private readonly RoleInterface $roleService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = ViewUserAction::run();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = $this->roleService->getRoles(false);

        $user = null;

        return view('admin.users.create', compact('roles', 'user'));
    }

    public function store(CreateRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $this->service->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Staff member invited successfully.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = $this->roleService->getRoles(false);

        $isSelf = Auth::id() === $user->id;

        return view('admin.users.create', compact('user', 'roles', 'isSelf'));
    }

    public function update(EditRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $this->service->update($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $result = $this->service->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', $result['message']);
    }
}