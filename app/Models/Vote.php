<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, HasUuid, Auditable, SoftDeletes;

    protected $fillable = [
        'election_id',
        'position_id',
        'candidate_id',
        'voter_id',
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

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('is_valid', true);
    }
}