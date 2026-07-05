<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Auditable;
}