<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_type',
        'name',
        'phone',
        'email',
        'tax_number',
        'address',
        'opening_balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(FinanceInvoice::class, 'party_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(FinanceVoucher::class, 'party_id');
    }

    public function financeEntries(): HasMany
    {
        return $this->hasMany(FinanceEntry::class, 'party_id');
    }
}
