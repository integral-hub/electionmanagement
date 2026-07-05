<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationField\CreateRequest;
use App\Models\Election;
use App\Models\ElectionSetting;
use App\Services\Interfaces\RegistrationFieldInterface;
use Illuminate\Http\RedirectResponse;

class RegistrationFieldController extends Controller
{
    public function __construct(
        private readonly RegistrationFieldInterface $service
    ) {}

    public function show(Election $election)
    {
        $this->authorize('view', $election->setting);

        $form = $election->registrationField;

        return view('admin.registration.show', compact('election', 'form'));
    }

    public function store(CreateRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('update', $election->setting);

        $this->service->create(
            array_merge($request->validated(), [
                'election_id' => $election->id,
            ])
        );

        return back()->with('success', 'Registration form created.');
    }

    public function update(CreateRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('update', $election->setting);

        $form = $election->registrationField;

        $this->service->update($form, $request->validated());

        return back()->with('success', 'Registration form updated.');
    }

    public function destroy(Election $election): RedirectResponse
    {
        $this->authorize('update', $election->setting);

        $result = $this->service->delete($election->registrationField);

        if ($result['status']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }
}