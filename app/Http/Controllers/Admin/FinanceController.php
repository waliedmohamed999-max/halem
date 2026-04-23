<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AccountingAccount;
use App\Models\AccountingJournal;
use App\Models\Branch;
use App\Models\FinanceEntry;
use App\Support\AccountingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use XLSXWriter;

class FinanceController extends Controller
{
    public function __construct(private readonly AccountingService $accountingService)
    {
    }

    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $statsFiltered = $this->buildStats(clone $query);
        $entries = $query->paginate(20)->withQueryString();
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $statsGlobal = $this->buildStats();

        return view('admin.finance.index', compact('entries', 'branches', 'statsFiltered', 'statsGlobal'));
    }

    public function create()
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.finance.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_type' => ['required', 'in:income,expense'],
            'entry_kind' => ['required', 'in:incoming_invoice,outgoing_invoice,expense,other'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:150'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'entry_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'record_status' => ['required', 'in:posted,void'],
        ]);

        $data['created_by'] = auth()->id();
        $entry = FinanceEntry::query()->create($data);
        $this->accountingService->syncFinanceEntry($entry);

        return redirect()->route('admin.finance.index', app()->getLocale())->with('success', 'Saved successfully');
    }

    public function show(FinanceEntry $finance)
    {
        $finance->load(['branch', 'appointment', 'creator', 'journal.lines.account']);

        return view('admin.finance.show', compact('finance'));
    }

    public function edit(FinanceEntry $finance)
    {
        if ($finance->entry_kind === 'appointment') {
            return redirect()->route('admin.finance.index', app()->getLocale())->with('success', 'Appointment finance entries are updated from appointments only');
        }

        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.finance.edit', compact('finance', 'branches'));
    }

    public function update(Request $request, FinanceEntry $finance)
    {
        if ($finance->entry_kind === 'appointment') {
            return redirect()->route('admin.finance.index', app()->getLocale())->with('success', 'Appointment finance entries are updated from appointments only');
        }

        $data = $request->validate([
            'entry_type' => ['required', 'in:income,expense'],
            'entry_kind' => ['required', 'in:incoming_invoice,outgoing_invoice,expense,other'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'title' => ['required', 'string', 'max:255'],
            'invoice_number' => ['nullable', 'string', 'max:150'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'entry_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'record_status' => ['required', 'in:posted,void'],
        ]);

        $finance->update($data);
        $this->accountingService->syncFinanceEntry($finance);

        return redirect()->route('admin.finance.index', app()->getLocale())->with('success', 'Updated successfully');
    }

    public function destroy(FinanceEntry $finance)
    {
        if ($finance->entry_kind === 'appointment') {
            return back()->with('success', 'Delete appointment to remove its finance entry');
        }

        $finance->update(['record_status' => 'void']);
        $this->accountingService->syncFinanceEntry($finance);
        $finance->delete();

        return redirect()->route('admin.finance.index', app()->getLocale())->with('success', 'Deleted successfully');
    }

    public function syncAppointments()
    {
        $appointments = Appointment::query()->get();
        $count = 0;

        foreach ($appointments as $appointment) {
            $party = $this->accountingService->customerParty($appointment->patient_name, $appointment->patient_phone);

            FinanceEntry::query()->updateOrCreate(
                [
                    'appointment_id' => $appointment->id,
                    'entry_type' => 'income',
                    'entry_kind' => 'appointment',
                ],
                [
                    'branch_id' => $appointment->branch_id,
                    'party_id' => $party?->id,
                    'cost_center_id' => $this->accountingService->costCenterForBranch($appointment->branch_id)?->id,
                    'created_by' => auth()->id(),
                    'ledger_context' => 'appointment',
                    'source_type' => 'appointment',
                    'source_id' => $appointment->id,
                    'title' => 'Appointment #' . $appointment->id . ' - ' . $appointment->patient_name,
                    'invoice_number' => 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT),
                    'counterparty' => $appointment->patient_name,
                    'amount' => (float) ($appointment->price ?? 0),
                    'entry_date' => $appointment->preferred_date ?: now()->toDateString(),
                    'payment_method' => 'cash',
                    'notes' => $appointment->notes,
                    'record_status' => $appointment->status === 'canceled' ? 'void' : 'posted',
                ]
            );
            $count++;
        }

        $this->accountingService->syncAllFinanceEntries();

        return redirect()
            ->route('admin.finance.index', app()->getLocale())
            ->with('success', (app()->getLocale() === 'ar' ? 'تم مزامنة قيود الحجوزات: ' : 'Appointments synced: ') . $count);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $rows = $this->buildFilteredQuery($request, false)->latest('entry_date')->latest('id')->get();
        $filename = $this->buildExportFilename($request, 'csv');

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Date', 'Type', 'Kind', 'Title', 'Invoice', 'Counterparty', 'Branch', 'Amount', 'Payment Method', 'Status', 'Notes']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    optional($row->entry_date)->format('Y-m-d'),
                    $row->entry_type,
                    $row->entry_kind,
                    $row->title,
                    $row->invoice_number,
                    $row->counterparty,
                    $row->branch?->name,
                    (float) $row->amount,
                    $row->payment_method,
                    $row->record_status,
                    preg_replace('/\s+/', ' ', (string) $row->notes),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportXlsx(Request $request)
    {
        $entries = $this->buildFilteredQuery($request, false)->latest('entry_date')->latest('id')->get();
        $income = $entries->where('entry_type', 'income')->values();
        $expense = $entries->where('entry_type', 'expense')->values();
        $filename = $this->buildExportFilename($request, 'xlsx');
        $tmpPath = storage_path('app/tmp_' . uniqid('finance_', true) . '.xlsx');

        $writer = new XLSXWriter();
        $writer->setAuthor('Dr Halim Dental Admin');

        $header = [
            'ID' => 'integer',
            'Date' => 'string',
            'Type' => 'string',
            'Kind' => 'string',
            'Title' => 'string',
            'Invoice' => 'string',
            'Counterparty' => 'string',
            'Branch' => 'string',
            'Amount' => 'price',
            'Payment Method' => 'string',
            'Status' => 'string',
            'Notes' => 'string',
        ];

        $postedIncomeAmount = (float) $entries->where('record_status', 'posted')->where('entry_type', 'income')->sum('amount');
        $postedExpenseAmount = (float) $entries->where('record_status', 'posted')->where('entry_type', 'expense')->sum('amount');

        $writer->writeSheetHeader('Summary', ['Metric' => 'string', 'Value' => 'string']);
        $writer->writeSheetRow('Summary', ['Total Entries', (string) $entries->count()]);
        $writer->writeSheetRow('Summary', ['Income Entries', (string) $income->count()]);
        $writer->writeSheetRow('Summary', ['Expense Entries', (string) $expense->count()]);
        $writer->writeSheetRow('Summary', ['Posted Income Total', number_format($postedIncomeAmount, 2, '.', '')]);
        $writer->writeSheetRow('Summary', ['Posted Expense Total', number_format($postedExpenseAmount, 2, '.', '')]);
        $writer->writeSheetRow('Summary', ['Net', number_format($postedIncomeAmount - $postedExpenseAmount, 2, '.', '')]);
        $writer->writeSheetRow('Summary', ['Generated At', now()->format('Y-m-d H:i:s')]);

        $writer->writeSheetHeader('Income', $header);
        foreach ($income as $row) {
            $writer->writeSheetRow('Income', $this->xlsxRow($row));
        }

        $writer->writeSheetHeader('Expenses', $header);
        foreach ($expense as $row) {
            $writer->writeSheetRow('Expenses', $this->xlsxRow($row));
        }

        $writer->writeToFile($tmpPath);

        return response()->download($tmpPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function reportPdf(Request $request)
    {
        $period = (string) $request->input('period', 'day');
        $query = $this->buildFilteredQuery($request, false);

        if ($period === 'month') {
            $query->whereBetween('entry_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()]);
        } else {
            $query->whereDate('entry_date', now()->toDateString());
        }

        $entries = $query->latest('entry_date')->latest('id')->get();
        $income = (float) $entries->where('entry_type', 'income')->where('record_status', 'posted')->sum('amount');
        $expense = (float) $entries->where('entry_type', 'expense')->where('record_status', 'posted')->sum('amount');
        $net = $income - $expense;

        $pdf = Pdf::loadView('pdf.finance-report', [
            'entries' => $entries,
            'income' => $income,
            'expense' => $expense,
            'net' => $net,
            'period' => $period,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('finance_report_' . $period . '_' . now()->format('Ymd_His') . '.pdf');
    }

    public function cashbox()
    {
        $postedCash = FinanceEntry::query()
            ->where('record_status', 'posted')
            ->where(function ($q): void {
                $q->where('payment_method', 'cash')->orWhereNull('payment_method');
            });

        $cashIn = (float) (clone $postedCash)->where('entry_type', 'income')->sum('amount');
        $cashOut = (float) (clone $postedCash)->where('entry_type', 'expense')->sum('amount');
        $balance = $cashIn - $cashOut;

        $todayIn = (float) FinanceEntry::query()->whereDate('entry_date', now()->toDateString())->where('record_status', 'posted')->where('entry_type', 'income')->where('payment_method', 'cash')->sum('amount');
        $todayOut = (float) FinanceEntry::query()->whereDate('entry_date', now()->toDateString())->where('record_status', 'posted')->where('entry_type', 'expense')->where('payment_method', 'cash')->sum('amount');

        $recent = FinanceEntry::query()
            ->where('record_status', 'posted')
            ->where('payment_method', 'cash')
            ->latest('entry_date')
            ->latest('id')
            ->take(20)
            ->get();

        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.finance.cashbox', compact('balance', 'cashIn', 'cashOut', 'todayIn', 'todayOut', 'recent', 'branches'));
    }

    public function storeCashboxMovement(Request $request)
    {
        $data = $request->validate([
            'movement_type' => ['required', 'in:in,out'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $entryType = $data['movement_type'] === 'in' ? 'income' : 'expense';
        $entryKind = $data['movement_type'] === 'in' ? 'incoming_invoice' : 'outgoing_invoice';

        $entry = FinanceEntry::query()->create([
            'entry_type' => $entryType,
            'entry_kind' => $entryKind,
            'branch_id' => $data['branch_id'] ?? null,
            'cost_center_id' => $this->accountingService->costCenterForBranch($data['branch_id'] ?? null)?->id,
            'created_by' => auth()->id(),
            'ledger_context' => $data['movement_type'] === 'in' ? 'other_income' : 'expense',
            'source_type' => 'cashbox',
            'source_id' => now()->timestamp,
            'title' => $data['title'],
            'invoice_number' => 'CBX-' . now()->format('YmdHis'),
            'counterparty' => $data['counterparty'] ?? null,
            'amount' => (float) $data['amount'],
            'entry_date' => $data['entry_date'],
            'payment_method' => 'cash',
            'notes' => $data['notes'] ?? null,
            'record_status' => 'posted',
        ]);

        $this->accountingService->syncFinanceEntry($entry);

        return redirect()->route('admin.finance.cashbox', app()->getLocale())->with('success', 'Cash movement saved successfully');
    }

    public function accounting(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $accountId = $request->input('account_id');

        $accounts = AccountingAccount::query()->where('is_active', true)->orderBy('code')->get();
        $journalsQuery = AccountingJournal::query()->with(['lines.account', 'financeEntry.branch'])->where('status', 'posted');

        if ($dateFrom) {
            $journalsQuery->whereDate('journal_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $journalsQuery->whereDate('journal_date', '<=', $dateTo);
        }
        if ($accountId) {
            $journalsQuery->whereHas('lines', fn ($q) => $q->where('account_id', $accountId));
        }

        $journals = $journalsQuery->latest('journal_date')->latest('id')->paginate(20)->withQueryString();

        $trialBalance = AccountingAccount::query()
            ->withSum(['lines as debit_sum' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) {
                    $q->whereHas('journal', fn ($jq) => $jq->whereDate('journal_date', '>=', $dateFrom)->where('status', 'posted'));
                }
                if ($dateTo) {
                    $q->whereHas('journal', fn ($jq) => $jq->whereDate('journal_date', '<=', $dateTo)->where('status', 'posted'));
                }
            }], 'debit')
            ->withSum(['lines as credit_sum' => function ($q) use ($dateFrom, $dateTo) {
                if ($dateFrom) {
                    $q->whereHas('journal', fn ($jq) => $jq->whereDate('journal_date', '>=', $dateFrom)->where('status', 'posted'));
                }
                if ($dateTo) {
                    $q->whereHas('journal', fn ($jq) => $jq->whereDate('journal_date', '<=', $dateTo)->where('status', 'posted'));
                }
            }], 'credit')
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                $debit = (float) ($account->debit_sum ?? 0);
                $credit = (float) ($account->credit_sum ?? 0);
                $account->balance = $account->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;
                return $account;
            });

        $summary = [
            'accounts' => $accounts->count(),
            'journals' => AccountingJournal::query()->where('status', 'posted')->count(),
            'debits' => (float) $trialBalance->sum('debit_sum'),
            'credits' => (float) $trialBalance->sum('credit_sum'),
            'revenues' => (float) $trialBalance->where('category', 'revenue')->sum('balance'),
            'expenses' => (float) $trialBalance->where('category', 'expense')->sum('balance'),
        ];
        $summary['net_profit'] = $summary['revenues'] - $summary['expenses'];

        return view('admin.finance.accounting', compact('accounts', 'journals', 'trialBalance', 'summary'));
    }

    public function syncAccounting()
    {
        $count = $this->accountingService->syncAllFinanceEntries();

        return redirect()->route('admin.finance.accounting', app()->getLocale())
            ->with('success', 'Accounting journals synced: ' . $count);
    }

    private function buildStats(?Builder $base = null): array
    {
        $statsBase = $base ? (clone $base)->where('record_status', 'posted') : FinanceEntry::query()->where('record_status', 'posted');
        $stats = [
            'entries_count' => (int) ((clone $statsBase)->count() ?? 0),
            'income_total' => (float) ((clone $statsBase)->where('entry_type', 'income')->sum('amount') ?? 0),
            'expense_total' => (float) ((clone $statsBase)->where('entry_type', 'expense')->sum('amount') ?? 0),
            'appointment_income' => (float) ((clone $statsBase)->where('entry_kind', 'appointment')->sum('amount') ?? 0),
            'incoming_invoices' => (float) ((clone $statsBase)->where('entry_kind', 'incoming_invoice')->sum('amount') ?? 0),
            'outgoing_invoices' => (float) ((clone $statsBase)->where('entry_kind', 'outgoing_invoice')->sum('amount') ?? 0),
        ];
        $stats['net'] = $stats['income_total'] - $stats['expense_total'];

        return $stats;
    }

    private function buildFilteredQuery(Request $request, bool $applySorting = true): Builder
    {
        $query = FinanceEntry::query()->with(['branch', 'appointment', 'creator']);

        if ($request->filled('q')) {
            $term = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($term): void {
                $builder
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('invoice_number', 'like', "%{$term}%")
                    ->orWhere('counterparty', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%");
            });
        }

        if ($request->filled('entry_type')) {
            $query->where('entry_type', (string) $request->input('entry_type'));
        }

        if ($request->filled('entry_kind')) {
            $query->where('entry_kind', (string) $request->input('entry_kind'));
        }

        if ($request->filled('record_status')) {
            $query->where('record_status', (string) $request->input('record_status'));
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', (int) $request->input('branch_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', (string) $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', (string) $request->input('date_to'));
        }

        if ($applySorting) {
            $allowedSorts = ['entry_date', 'amount', 'id'];
            $sortBy = (string) $request->input('sort_by', 'entry_date');
            $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'entry_date';
            $sortDir = (string) $request->input('sort_dir', 'desc');
            $sortDir = in_array($sortDir, ['asc', 'desc'], true) ? $sortDir : 'desc';

            $query->orderBy($sortBy, $sortDir)->orderBy('id', $sortDir);
        }

        return $query;
    }

    private function xlsxRow(FinanceEntry $row): array
    {
        return [
            $row->id,
            optional($row->entry_date)->format('Y-m-d'),
            $row->entry_type,
            $row->entry_kind,
            $row->title,
            $row->invoice_number,
            $row->counterparty,
            $row->branch?->name,
            (float) $row->amount,
            $row->payment_method,
            $row->record_status,
            preg_replace('/\s+/', ' ', (string) $row->notes),
        ];
    }

    private function buildExportFilename(Request $request, string $extension): string
    {
        $parts = ['finance'];

        if ($request->filled('entry_type')) {
            $parts[] = (string) $request->input('entry_type');
        }
        if ($request->filled('entry_kind')) {
            $parts[] = (string) $request->input('entry_kind');
        }
        if ($request->filled('record_status')) {
            $parts[] = (string) $request->input('record_status');
        }
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $from = (string) $request->input('date_from', 'start');
            $to = (string) $request->input('date_to', 'end');
            $parts[] = $from . '_to_' . $to;
        }

        $base = implode('_', $parts) . '_' . now()->format('Ymd_His');
        $base = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base) ?: 'finance_export_' . now()->format('Ymd_His');

        return $base . '.' . $extension;
    }
}
