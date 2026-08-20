<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use HasUuid;
    use HasFormattedName;
    use Auditable;
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'organization_id',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile_photo_path' => 'array'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }


}