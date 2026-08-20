<?php

namespace App\Models;

use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationField extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'election_id',
        'fields',
        'active',
    ];

    protected $casts = [
        'fields' => 'array',
        'active' => 'boolean',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}