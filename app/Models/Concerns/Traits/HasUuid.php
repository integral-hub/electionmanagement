<?php

declare(strict_types=1);

namespace App\Models\Concerns\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
          static::creating(function (Model $model) {
               $model->uuid ??= Str::uuid()->toString();
          });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
