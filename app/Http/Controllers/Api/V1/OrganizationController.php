<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Organization\EditOrganization;
use App\Http\Requests\Organization\EditRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrganizationController extends Controller
{
    public function show(): JsonResponse
    {
        $org = Auth::user()->load('organization')->organization;

        $this->authorize('view', $org);

        return $this->success($org);
    }

    public function update(EditRequest $request): JsonResponse
    {
        $org = Auth::user()->organization;

        $this->authorize('update', $org);

        $org = EditOrganization::run($org, $request->payload());

        return $this->success($org, 'Organisation updated.');
    }
}
