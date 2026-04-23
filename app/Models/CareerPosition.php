<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerPosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title_ar',
        'title_en',
        'department_ar',
        'department_en',
        'location_ar',
        'location_en',
        'job_type',
        'experience_level',
        'summary_ar',
        'summary_en',
        'description_ar',
        'description_en',
        'requirements_ar',
        'requirements_en',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(CareerApplication::class);
    }

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->title_ar : ($this->title_en ?: $this->title_ar);
    }

    public function getDepartmentAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->department_ar ?: $this->department_en) : ($this->department_en ?: $this->department_ar);
    }

    public function getLocationAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->location_ar ?: $this->location_en) : ($this->location_en ?: $this->location_ar);
    }

    public function getSummaryAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? ($this->summary_ar ?: $this->summary_en) : ($this->summary_en ?: $this->summary_ar);
    }
}
