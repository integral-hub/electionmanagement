<?php

namespace App\Models;

use App\Models\Concerns\Traits\HasSlug;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Election extends Model
{
    use HasFactory, HasUuid, HasFormattedName, HasSlug;

    protected $fillable = [
        'uuid',
        'organization_id',
        'name',
        'slug',
        'description',
        'status',
        'created_by',
    ];

    // Relationships

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function registrationField()
    {
        return $this->belongsTo(RegistrationField::class);
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
        return $this->hasManyThrough(
                Voter::class,
                ElectionVoter::class,
                    'election_id', 
                    'id',          
                    'id',          
                    'voter_id'     
        );
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