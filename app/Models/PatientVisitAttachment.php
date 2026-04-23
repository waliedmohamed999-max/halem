<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientVisitAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_visit_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(PatientVisit::class, 'patient_visit_id');
    }
}

