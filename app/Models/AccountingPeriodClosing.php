<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountingPeriodClosing extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'period_key',
        'status',
        'income_total',
        'expense_total',
        'net_profit',
        'closed_at',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'income_total' => 'decimal:2',
        'expense_total' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
