<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\CreateRequest;
use App\Http\Requests\Position\EditRequest;
use App\Models\Election;
use App\Models\Position;
use App\Services\Interfaces\PositionInterface;
use Illuminate\Http\RedirectResponse;

class PositionController extends Controller
{
    public function __construct(
        private readonly PositionInterface $service
    ) {}

    public function create(Election $election)
    {
        $this->authorize('create', Position::class);

        return view('admin.positions.create', compact('election'));
    }

    public function store(CreateRequest $request, Election $election): RedirectResponse
    {
        $this->authorize('create', Position::class);

        $this->service->create(
            array_merge($request->validated(), [
                'election_id' => $election->id,
            ])
        );

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Position added.');
    }

    public function edit(Election $election, Position $position)
    {
        $this->authorize('update', $position);

        return view('admin.positions.create', compact('election', 'position'));
    }

    public function update(EditRequest $request, Election $election, Position $position): RedirectResponse 
    {
        $this->authorize('update', $position);

        $this->service->update($position, $request->validated());

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Position updated.');
    }

    public function destroy(Election $election, Position $position): RedirectResponse 
    {
        $this->authorize('delete', $position);

        $result = $this->service->delete($position);

        if (is_array($result) && $result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('admin.elections.show', $election)
            ->with('success', 'Position removed.');
    }
}