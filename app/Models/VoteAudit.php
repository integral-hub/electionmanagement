<?php

namespace App\Models;

use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoteAudit extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'voter_id',
        'election_id',
        'ip_address',
        'user_agent',
    ];

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}