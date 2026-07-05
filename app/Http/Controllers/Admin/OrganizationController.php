<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Organization\CreateOrganization;
use App\Actions\Organization\EditOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CreateRequest;
use App\Http\Requests\Organization\EditRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    public function register()
    {
        return view('auth.register');
    }

    public function storeRegister(CreateRequest $request): RedirectResponse
    {
        CreateOrganization::run($request->payload());

        return redirect()
            ->route('login')
            ->with('success', 'Organisation registered. Login to get started.');
    }

    public function show()
    {
        $org = Auth::user()->load('organization')->organization;

        $this->authorize('view', $org);

        return view('admin.organization.show', compact('org'));
    }

    public function edit()
    {
        $org = Auth::user()->organization;

        $this->authorize('update', $org);

        return view('admin.organization.edit', compact('org'));
    }

    public function update(EditRequest $request): RedirectResponse
    {
        $org = Auth::user()->organization;

        $this->authorize('update', $org);

        EditOrganization::run($org, $request->payload());

        return redirect()
            ->route('admin.organization.show')
            ->with('success', 'Organisation updated.');
    }
}