<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title_ar', 'title_en', 'content_ar', 'content_en', 'slug', 'is_active',
        'seo_title', 'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
