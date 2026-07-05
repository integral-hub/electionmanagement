<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use Auditable;
}