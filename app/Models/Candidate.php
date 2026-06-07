<?php

namespace App\Models;

use App\Models\Concerns\Traits\HasFormattedName;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory, HasUuid, HasFormattedName;

    protected $fillable = [
        'uuid',
        'election_id',
        'position_id',
        'name',
        'photo',
        'bio',
        'manifesto',
        'status',
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
}