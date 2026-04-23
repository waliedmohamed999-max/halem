<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientVisit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'branch_id',
        'doctor_id',
        'appointment_id',
        'visit_date',
        'visit_time',
        'visit_status',
        'chief_complaint',
        'clinical_findings',
        'diagnosis',
        'treatment_plan',
        'procedure_done',
        'prescription',
        'next_visit_date',
        'notes',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'next_visit_date' => 'date',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PatientVisitAttachment::class)->latest();
    }
}
