<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountingJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'finance_entry_id',
        'party_id',
        'cost_center_id',
        'journal_no',
        'journal_date',
        'reference_type',
        'reference_id',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'journal_date' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(AccountingJournalLine::class, 'journal_id')->orderBy('sort_order');
    }

    public function financeEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceEntry::class, 'finance_entry_id');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(FinanceParty::class, 'party_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(FinanceCostCenter::class, 'cost_center_id');
    }
}
