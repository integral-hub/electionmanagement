<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class Voter extends Authenticatable
{
    use HasFactory, HasUuid, HasFormattedName, Notifiable, Auditable, SoftDeletes;

    protected $fillable = [
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
        'password' => 'hashed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    public function voteStatus(Election $election): ?string
    {
        $vote = $election->votes()
            ->where('voter_id', $this->id)
            ->first();

        if (!$vote) {
            return null; // Nil
        }

        return !$vote->is_valid ? 'revoked' : 'voted';
    }
    public function hasVote(Election $election): bool
    {
        return ! is_null($this->voteStatus($election));
    }

    // Relationships
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function elections()
    {
        return $this->belongsToMany
                    (Election::class,'election_voters','voter_id','election_id')
                      ->withTimestamps()
                      ->withPivot('status', 'validated_by', 'validated_at');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    public function uniqueData()
    {
        return $this->hasMany(VoterUniqueData::class);
    }
    public function getUniqueValue($key)
    {
        return $this->uniqueData
            ->firstWhere('field_name', $key)
            ?->value;
    }


}
