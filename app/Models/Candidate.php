<?php

namespace App\Models;

use App\Enums\CandidateStatusEnum;
use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory, HasUuid, HasFormattedName, Auditable;

    protected $fillable = [
        'election_id',
        'position_id',
        'name',
        'photo',
        'bio',
        'manifesto',
        'status',
    ];

    protected $casts = [
        'photo' => 'array',
    ];
    // Relationships

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CandidateStatusEnum::Active->value);
    }

}