<?php

namespace App\Models;

use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Voter extends Model
{
    use HasFactory, HasUuid, HasFormattedName, Notifiable;

    protected $fillable = [
        'uuid',
        'organization_id',
        'voter_data',
        'batch_code',
        'phone',
        'email',
        'password',
        'is_verified_email',
        'is_verified_phone',
        'last_login_at',
    ];

    protected $casts = [
        'voter_data' => 'array',
        'is_verified_email' => 'boolean',
        'is_verified_phone' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    // Relationships

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function elections()
    {
        return $this->hasManyThrough(
                Election::class,
                ElectionVoter::class,
                    'voter_id',   
                    'id',          
                    'id',          
                    'election_id' 
        );
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    public function uniqueData()
    {
        return $this->hasMany(VoterUniqueData::class);
    }

}
