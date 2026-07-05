<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectionSetting extends Model
{
    use HasFactory, HasUuid, Auditable;

    protected $fillable = [
        'uuid',
        'election_id',
        'registration_mode',
        'voters_verification_requirement', //{"email": true, "phone": false,"image_compare": false}
        'vote_before_validation',
        'login_fields',
        'voters_require_2fa',
        'voters_2fa_type',
        'is_started',
        'voting_start',
        'voting_end',
    ];

    protected $casts = [
        'voters_verification_requirement' => 'array',
        'vote_before_validation' => 'boolean',
        'login_fields' => 'array',
        'voters_require_2fa' => 'boolean',
        'is_started' => 'boolean',
        'voting_start' => 'datetime',
        'voting_end' => 'datetime',
    ];

    protected $attributes = [
        'voters_verification_requirement' => 
                '{"email":true}',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}