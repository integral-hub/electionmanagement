<?php

namespace App\Models;

use App\Models\Concerns\Traits\HasSlug;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory, HasUuid, HasFormattedName, HasSlug;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'email',
        'phone',
        'logo',
        'website',
        'access_token',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function elections()
    {
        return $this->hasMany(Election::class);
    }

}