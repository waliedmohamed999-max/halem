<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medium extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = ['disk', 'path', 'collection', 'alt_text'];
}
