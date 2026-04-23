<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'day_of_week', 'day_label_ar', 'day_label_en', 'is_open',
        'open_at', 'close_at', 'is_emergency', 'emergency_text', 'emergency_phone',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'is_emergency' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
