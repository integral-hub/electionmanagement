<?php

declare(strict_types=1);

namespace App\Actions\Organization;

use App\Models\Organization;
use Lorisleiva\Actions\Concerns\AsAction;

class EditOrganization
{
    use AsAction;

    public function handle(Organization $organization, array $data): Organization 
    {
        $organization->update($data);

        return $organization->refresh();
    }
}