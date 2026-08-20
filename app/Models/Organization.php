<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasSlug;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes, HasFactory, HasUuid, HasFormattedName, HasSlug, Auditable;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'logo',
        'website',
        'package_type',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'logo' => 'array'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function elections()
    {
        return $this->hasMany(Election::class);
    }
    public function token()
    {
        return $this->hasOne(OrganizationToken::class);
    }

}