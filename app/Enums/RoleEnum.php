<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleEnum: string
{
     case System_Admin = 'system admin';
     case Admin = 'administrator';

     public function defaultPermissions(): array
     {
          return PermissionEnum::getPermissionFor($this);
     }
}
