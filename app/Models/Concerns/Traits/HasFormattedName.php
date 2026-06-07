<?php

declare(strict_types=1);

namespace App\Models\Concerns\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasFormattedName
{
     protected function name(): Attribute
     {
          return Attribute::make(
               get: fn ($value) => Str::title($value),
               set: fn ($value) => Str::lower($value)
          );
     }

     protected function title(): Attribute
     {
          return Attribute::make(
               get: fn ($value) => Str::title($value),
               set: fn ($value) => Str::lower($value)
          );
     }


     protected function email(): Attribute
     {
          return Attribute::make(
               set: fn ($value) => Str::lower($value)
          );
     }
}
