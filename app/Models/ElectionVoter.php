<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ElectionVoter extends Pivot
{
    use Auditable;
    
    protected $table = 'election_voters';

    protected $fillable = [
        'election_id',
        'voter_id',
        'status',
        'validated_by',
        'validated_at',
    ];

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}