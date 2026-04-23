<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;

    protected $fillable = ['section_key', 'title_ar', 'title_en', 'payload', 'is_active', 'sort_order'];

    protected $casts = [
        'payload' => 'array',
        'is_active' => 'boolean',
    ];
}
