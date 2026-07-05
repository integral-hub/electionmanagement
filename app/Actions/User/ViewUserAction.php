<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lorisleiva\Actions\Concerns\AsAction;

class ViewUserAction
{
    use AsAction;

    public function handle()
    {

        return User::query()->where('organization_id', global_data('org_id'))
                ->whereNot('id', Auth::id())
                ->with('roles')
                ->paginate(15);
    }

}