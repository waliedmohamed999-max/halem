<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_type',
        'branch_id',
        'cost_center_id',
        'party_id',
        'finance_entry_id',
        'invoice_no',
        'uuid',
        'currency_code',
        'issue_date',
        'supply_date',
        'due_date',
        'payment_terms',
        'invoice_scope',
        'reference_number',
        'status',
        'zatca_status',
        'zatca_xml_path',
        'zatca_invoice_hash',
        'zatca_signature',
        'zatca_qr_payload',
        'zatca_validation',
        'zatca_last_response',
        'zatca_reported_at',
        'zatca_cleared_at',
        'subtotal',
        'discount',
        'tax_rate',
        'tax',
        'total',
        'paid_amount',
        'balance_due',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'supply_date' => 'date',
        'due_date' => 'date',
        'zatca_validation' => 'array',
        'zatca_last_response' => 'array',
        'zatca_reported_at' => 'datetime',
        'zatca_cleared_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
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

    public function financeEntry(): BelongsTo
    {
        return $this->belongsTo(FinanceEntry::class, 'finance_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(FinanceInvoiceItem::class, 'invoice_id');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(FinanceVoucher::class, 'invoice_id');
    }
}
