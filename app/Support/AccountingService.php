<?php

namespace App\Support;

use App\Models\AccountingAccount;
use App\Models\AccountingJournal;
use App\Models\FinanceCostCenter;
use App\Models\FinanceEntry;
use App\Models\FinanceParty;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public function syncFinanceEntry(FinanceEntry $financeEntry): void
    {
        DB::transaction(function () use ($financeEntry): void {
            $financeEntry = $financeEntry->fresh();

            if (! $financeEntry) {
                return;
            }

            $journal = AccountingJournal::query()->firstOrNew([
                'finance_entry_id' => $financeEntry->id,
            ]);

            $journal->fill([
                'journal_no' => $journal->journal_no ?: $this->generateJournalNumber($financeEntry),
                'journal_date' => $financeEntry->entry_date,
                'reference_type' => $financeEntry->source_type ?: 'finance_entry',
                'reference_id' => $financeEntry->source_id ?: $financeEntry->id,
                'description' => $financeEntry->title,
                'status' => $financeEntry->record_status === 'posted' ? 'posted' : 'void',
                'created_by' => $financeEntry->created_by,
                'party_id' => $financeEntry->party_id,
                'cost_center_id' => $financeEntry->cost_center_id,
            ]);
            $journal->save();

            $journal->lines()->delete();

            $debitAccount = $this->resolveDebitAccount($financeEntry);
            $creditAccount = $this->resolveCreditAccount($financeEntry);
            $amount = (float) $financeEntry->amount;

            $journal->lines()->create([
                'account_id' => $debitAccount->id,
                'branch_id' => $financeEntry->branch_id,
                'party_id' => $financeEntry->party_id,
                'cost_center_id' => $financeEntry->cost_center_id,
                'description' => $financeEntry->title,
                'debit' => $amount,
                'credit' => 0,
                'sort_order' => 1,
            ]);

            $journal->lines()->create([
                'account_id' => $creditAccount->id,
                'branch_id' => $financeEntry->branch_id,
                'party_id' => $financeEntry->party_id,
                'cost_center_id' => $financeEntry->cost_center_id,
                'description' => $financeEntry->title,
                'debit' => 0,
                'credit' => $amount,
                'sort_order' => 2,
            ]);
        });
    }

    public function syncAllFinanceEntries(): int
    {
        $count = 0;

        FinanceEntry::query()->orderBy('id')->chunkById(100, function ($entries) use (&$count): void {
            foreach ($entries as $entry) {
                $this->syncFinanceEntry($entry);
                $count++;
            }
        });

        return $count;
    }

    private function generateJournalNumber(FinanceEntry $financeEntry): string
    {
        return 'JV-' . $financeEntry->entry_date->format('Ymd') . '-' . str_pad((string) $financeEntry->id, 6, '0', STR_PAD_LEFT);
    }

    private function resolveDebitAccount(FinanceEntry $financeEntry): AccountingAccount
    {
        $context = $financeEntry->ledger_context ?: $financeEntry->entry_kind;

        if (in_array($context, ['receipt_voucher', 'appointment', 'customer_invoice', 'other_income'], true)) {
            return $financeEntry->payment_method === 'credit'
                ? $this->accountByCode('1140')
                : $this->cashLikeAccount($financeEntry);
        }

        if ($context === 'payment_voucher') {
            return $this->accountByCode('2110');
        }

        if ($context === 'supplier_invoice') {
            return $this->expenseLikeAccount($financeEntry);
        }

        if ($financeEntry->entry_type === 'income') {
            return $this->cashLikeAccount($financeEntry);
        }

        if ($financeEntry->payment_method === 'credit') {
            return $this->expenseLikeAccount($financeEntry);
        }

        if (in_array($financeEntry->entry_kind, ['expense', 'outgoing_invoice'], true)) {
            return $this->accountByCode('5110');
        }

        return $this->accountByCode('5120');
    }

    private function resolveCreditAccount(FinanceEntry $financeEntry): AccountingAccount
    {
        $context = $financeEntry->ledger_context ?: $financeEntry->entry_kind;

        if (in_array($context, ['appointment', 'customer_invoice'], true)) {
            return $this->accountByCode('4110');
        }

        if ($context === 'receipt_voucher') {
            return $this->accountByCode('1140');
        }

        if (in_array($context, ['supplier_invoice', 'payment_voucher'], true)) {
            return $financeEntry->payment_method === 'credit'
                ? $this->accountByCode('2110')
                : $this->cashLikeAccount($financeEntry);
        }

        if ($financeEntry->entry_type === 'income') {
            if ($financeEntry->entry_kind === 'appointment') {
                return $this->accountByCode('4110');
            }

            return $this->accountByCode('4120');
        }

        if ($financeEntry->payment_method === 'credit') {
            return $this->accountByCode('2110');
        }

        return $this->cashLikeAccount($financeEntry);
    }

    private function cashLikeAccount(FinanceEntry $financeEntry): AccountingAccount
    {
        return match ($financeEntry->payment_method) {
            'transfer' => $this->accountByCode('1120'),
            'wallet' => $this->accountByCode('1130'),
            'card' => $this->accountByCode('1120'),
            default => $this->accountByCode('1110'),
        };
    }

    private function expenseLikeAccount(FinanceEntry $financeEntry): AccountingAccount
    {
        $meta = (array) ($financeEntry->meta ?? []);

        if (($meta['expense_category'] ?? null) === 'inventory') {
            return $this->accountByCode('1150');
        }

        return in_array($financeEntry->entry_kind, ['expense', 'outgoing_invoice'], true)
            ? $this->accountByCode('5110')
            : $this->accountByCode('5120');
    }

    private function accountByCode(string $code): AccountingAccount
    {
        return AccountingAccount::query()->where('code', $code)->firstOrFail();
    }

    public function costCenterForBranch(?int $branchId): ?FinanceCostCenter
    {
        if (! $branchId) {
            return null;
        }

        return FinanceCostCenter::query()->where('branch_id', $branchId)->where('is_active', true)->orderBy('id')->first();
    }

    public function customerParty(string $name, ?string $phone = null): ?FinanceParty
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        return FinanceParty::query()->firstOrCreate(
            ['name' => $name],
            [
                'party_type' => 'customer',
                'phone' => $phone,
                'is_active' => true,
            ]
        );
    }
}
