<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'item_code',
        'name_ar',
        'name_en',
        'category',
        'unit',
        'min_stock',
        'current_stock',
        'average_cost',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'min_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'item_id');
    }

    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
