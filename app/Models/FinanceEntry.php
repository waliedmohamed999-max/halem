<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'entry_type',
        'entry_kind',
        'appointment_id',
        'branch_id',
        'party_id',
        'cost_center_id',
        'created_by',
        'ledger_context',
        'source_type',
        'source_id',
        'title',
        'invoice_number',
        'counterparty',
        'amount',
        'entry_date',
        'payment_method',
        'notes',
        'meta',
        'record_status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
        'meta' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(FinanceParty::class, 'party_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(FinanceCostCenter::class, 'cost_center_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journal(): HasOne
    {
        return $this->hasOne(AccountingJournal::class, 'finance_entry_id');
    }
}
