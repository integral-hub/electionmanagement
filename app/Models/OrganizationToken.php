<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationToken extends Model
{
    protected $table = 'organization_tokens';

    protected $fillable = [
        'name',
        'token',
        'organization_id',
        'is_used',
        'max_elections'
    ];
}