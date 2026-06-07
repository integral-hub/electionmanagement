<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoterUniqueData extends Model
{
    protected $table = 'voter_unique_data';

    protected $fillable = [
        'voter_id',
        'field_name',
        'value'
    ];

    /**
     * Relationship
     */
    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }
}
