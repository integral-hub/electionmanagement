<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasSlug;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use App\Traits\VoteEligibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Election extends Model
{
    use HasFactory, HasUuid, HasFormattedName, HasSlug, Auditable, VoteEligibility;

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'status',
        'created_by',
    ];
    protected $appends = [
        'is_portal_ready',
        'is_registration_open',
    ];

    public function getIsPortalReadyAttribute(): bool
    {
        return $this->portalReady($this);
    }

    public function getIsRegistrationOpenAttribute(): bool
    {
        return $this->registrationOpen($this);
    }
    public function getCanVoteAttribute()
    {
        return $this->canVote($this);
    }

    // Relationships

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function registrationField()
    {
        return $this->hasOne(RegistrationField::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function voters()
    {
        return $this->belongsToMany(Voter::class, 'election_voters')
            ->using(\App\Models\ElectionVoter::class)
            ->withPivot('status', 'validated_by', 'validated_at');
    }

    public function setting()
    {
        return $this->hasOne(ElectionSetting::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}