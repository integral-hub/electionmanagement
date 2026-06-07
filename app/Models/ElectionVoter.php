<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionVoter extends Model
{
    protected $table = 'election_voters';

    protected $fillable = [
        'election_id',
        'voter_id',
        'status',
        'validated_by',
        'validated_at',
        'batch_code',
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