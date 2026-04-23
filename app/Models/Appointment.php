<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id', 'patient_name', 'patient_phone', 'branch_id', 'service_id', 'preferred_date',
        'booking_type', 'price', 'preferred_time', 'notes', 'status', 'source',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'preferred_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function visit(): HasOne
    {
        return $this->hasOne(PatientVisit::class);
    }

    public function financeEntries(): HasMany
    {
        return $this->hasMany(FinanceEntry::class);
    }
}
