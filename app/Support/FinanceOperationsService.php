<?php

namespace App\Support;

use App\Models\AccountingPeriodClosing;
use App\Models\FinanceEntry;
use App\Models\FinanceInvoice;
use App\Models\FinanceVoucher;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FinanceOperationsService
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    public function assertPeriodOpen(string $date): void
    {
        $periodKey = date('Y-m', strtotime($date));

        $isClosed = AccountingPeriodClosing::query()
            ->where('period_key', $periodKey)
            ->where('status', 'closed')
            ->exists();

        if ($isClosed) {
            throw new RuntimeException('This financial period is closed.');
        }
    }

    public function syncInvoice(FinanceInvoice $invoice): FinanceEntry
    {
        return DB::transaction(function () use ($invoice): FinanceEntry {
            $invoice = $invoice->fresh(['party']);

            $this->assertPeriodOpen($invoice->issue_date->format('Y-m-d'));

            $entryType = $invoice->invoice_type === 'customer' ? 'income' : 'expense';
            $entryKind = $invoice->invoice_type === 'customer' ? 'incoming_invoice' : 'outgoing_invoice';
            $ledgerContext = $invoice->invoice_type === 'customer' ? 'customer_invoice' : 'supplier_invoice';
            $paymentMethod = $invoice->payment_terms === 'cash' ? 'cash' : 'credit';

            $entry = FinanceEntry::query()->firstOrNew([
                'source_type' => 'finance_invoice',
                'source_id' => $invoice->id,
            ]);

            $entry->fill([
                'entry_type' => $entryType,
                'entry_kind' => $entryKind,
                'branch_id' => $invoice->branch_id,
                'party_id' => $invoice->party_id,
                'cost_center_id' => $invoice->cost_center_id,
                'created_by' => $invoice->created_by,
                'ledger_context' => $ledgerContext,
                'title' => ($invoice->invoice_type === 'customer' ? 'Customer Invoice ' : 'Supplier Invoice ') . $invoice->invoice_no,
                'invoice_number' => $invoice->invoice_no,
                'counterparty' => $invoice->party?->name,
                'amount' => (float) $invoice->total,
                'entry_date' => $invoice->issue_date,
                'payment_method' => $paymentMethod,
                'notes' => $invoice->notes,
                'meta' => [
                    'invoice_type' => $invoice->invoice_type,
                    'payment_terms' => $invoice->payment_terms,
                ],
                'record_status' => $invoice->status === 'cancelled' ? 'void' : 'posted',
            ]);
            $entry->save();

            if ((int) $invoice->finance_entry_id !== (int) $entry->id) {
                $invoice->forceFill(['finance_entry_id' => $entry->id])->save();
            }

            $this->accountingService->syncFinanceEntry($entry);
            $this->refreshInvoiceBalances($invoice->fresh());

            return $entry;
        });
    }

    public function syncVoucher(FinanceVoucher $voucher): FinanceEntry
    {
        return DB::transaction(function () use ($voucher): FinanceEntry {
            $voucher = $voucher->fresh(['party', 'invoice']);

            $this->assertPeriodOpen($voucher->voucher_date->format('Y-m-d'));

            $entry = FinanceEntry::query()->firstOrNew([
                'source_type' => 'finance_voucher',
                'source_id' => $voucher->id,
            ]);

            $entry->fill([
                'entry_type' => $voucher->voucher_type === 'receipt' ? 'income' : 'expense',
                'entry_kind' => 'other',
                'branch_id' => $voucher->branch_id,
                'party_id' => $voucher->party_id,
                'cost_center_id' => $voucher->cost_center_id,
                'created_by' => $voucher->created_by,
                'ledger_context' => $voucher->voucher_type === 'receipt' ? 'receipt_voucher' : 'payment_voucher',
                'title' => ($voucher->voucher_type === 'receipt' ? 'Receipt Voucher ' : 'Payment Voucher ') . $voucher->voucher_no,
                'invoice_number' => $voucher->voucher_no,
                'counterparty' => $voucher->party?->name,
                'amount' => (float) $voucher->amount,
                'entry_date' => $voucher->voucher_date,
                'payment_method' => $voucher->payment_method ?: 'cash',
                'notes' => $voucher->notes,
                'meta' => [
                    'invoice_id' => $voucher->invoice_id,
                    'voucher_type' => $voucher->voucher_type,
                ],
                'record_status' => $voucher->status === 'posted' ? 'posted' : 'void',
            ]);
            $entry->save();

            if ((int) $voucher->finance_entry_id !== (int) $entry->id) {
                $voucher->forceFill(['finance_entry_id' => $entry->id])->save();
            }

            $this->accountingService->syncFinanceEntry($entry);

            if ($voucher->invoice_id) {
                $this->refreshInvoiceBalances($voucher->invoice()->firstOrFail());
            }

            return $entry;
        });
    }

    public function refreshInvoiceBalances(FinanceInvoice $invoice): void
    {
        if ($invoice->payment_terms === 'cash' && $invoice->status !== 'cancelled') {
            $invoice->forceFill([
                'paid_amount' => (float) $invoice->total,
                'balance_due' => 0,
                'status' => 'paid',
            ])->save();

            return;
        }

        $paidAmount = (float) $invoice->vouchers()
            ->where('status', 'posted')
            ->sum('amount');

        $balanceDue = max((float) $invoice->total - $paidAmount, 0);

        $status = match (true) {
            $invoice->status === 'cancelled' => 'cancelled',
            $balanceDue <= 0.0001 => 'paid',
            $paidAmount > 0 => 'partially_paid',
            default => 'issued',
        };

        $invoice->forceFill([
            'paid_amount' => $paidAmount,
            'balance_due' => $balanceDue,
            'status' => $status,
        ])->save();
    }

    public function applyInventoryMovement(InventoryMovement $movement): void
    {
        DB::transaction(function () use ($movement): void {
            $movement = $movement->fresh(['item']);
            $item = $movement->item;

            if (! $item) {
                return;
            }

            $currentStock = (float) $item->current_stock;
            $currentAverage = (float) $item->average_cost;
            $quantity = (float) $movement->quantity;
            $unitCost = (float) $movement->unit_cost;

            if (in_array($movement->movement_type, ['receipt', 'adjustment_in'], true)) {
                $newStock = $currentStock + $quantity;
                $newAverage = $newStock > 0
                    ? (($currentStock * $currentAverage) + ($quantity * $unitCost)) / $newStock
                    : $currentAverage;
            } else {
                $newStock = max($currentStock - $quantity, 0);
                $newAverage = $currentAverage;
                if ($unitCost <= 0) {
                    $movement->forceFill([
                        'unit_cost' => $currentAverage,
                        'total_cost' => $quantity * $currentAverage,
                    ])->save();
                }
            }

            $item->forceFill([
                'current_stock' => $newStock,
                'average_cost' => round($newAverage, 2),
            ])->save();
        });
    }
}
