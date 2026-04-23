<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'career_position_id',
        'full_name',
        'phone',
        'email',
        'city',
        'experience_years',
        'cover_letter',
        'cv_file',
        'status',
        'admin_notes',
    ];

    public function position(): BelongsTo
    {
        return $this->belongsTo(CareerPosition::class, 'career_position_id');
    }
}
