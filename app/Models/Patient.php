<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'address',
        'date_of_birth',
        'gender',
        'occupation',
        'marital_status',
        'national_id',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_company',
        'insurance_number',
        'smoking_status',
        'allergies',
        'chronic_diseases',
        'current_medications',
        'previous_surgeries',
        'notes',
        'last_visit_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_visit_at' => 'datetime',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(PatientVisit::class)->latest('visit_date');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->latest('preferred_date')->latest('preferred_time');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDocument::class)->latest('document_date')->latest('id');
    }
}
