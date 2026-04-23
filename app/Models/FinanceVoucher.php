<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_type',
        'branch_id',
        'cost_center_id',
        'party_id',
        'invoice_id',
        'finance_entry_id',
        'voucher_no',
        'voucher_date',
        'payment_method',
        'amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'voucher_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(FinanceCostCenter::class, 'cost_center_id');
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(FinanceParty::class, 'party_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(FinanceInvoice::class, 'invoice_id');
    }

    public function financeEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceEntry::class, 'finance_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
