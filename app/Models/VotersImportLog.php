<?php

namespace App\Models;

use App\Models\Concerns\Traits\Auditable;
use App\Models\Concerns\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VotersImportLog extends Model
{
    use HasFactory, HasUuid, Auditable;

    protected $fillable = [
        'election_id',
        'file_name',
        'total_records',
        'batch_code',
        'uploaded_by',
    ];

    protected $casts = [
        'total_records' => 'integer',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}