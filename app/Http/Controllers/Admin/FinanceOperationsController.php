<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingAccount;
use App\Models\AccountingJournalLine;
use App\Models\AccountingPeriodClosing;
use App\Models\Branch;
use App\Models\FinanceCostCenter;
use App\Models\FinanceInvoice;
use App\Models\FinanceParty;
use App\Models\FinanceVoucher;
use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use App\Models\InventoryWarehouse;
use App\Models\Setting;
use App\Support\Code39Barcode;
use App\Support\FinanceOperationsService;
use App\Support\ZatcaInvoiceValidator;
use App\Support\ZatcaInvoiceWorkflowService;
use App\Support\ZatcaInvoiceQr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class FinanceOperationsController extends Controller
{
    public function __construct(
        private readonly FinanceOperationsService $operations,
        private readonly ZatcaInvoiceWorkflowService $zatcaWorkflow,
        private readonly ZatcaInvoiceValidator $zatcaValidator,
    )
    {
    }

    public function masterData()
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $costCenters = FinanceCostCenter::query()->with('branch')->latest()->get();
        $parties = FinanceParty::query()->latest()->paginate(15, ['*'], 'parties_page');

        return view('admin.finance.master-data', compact('branches', 'costCenters', 'parties'));
    }

    public function storeCostCenter(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'code' => ['required', 'string', 'max:50', 'unique:finance_cost_centers,code'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        FinanceCostCenter::query()->create($data + ['is_active' => true]);

        return redirect()->route('admin.finance.master-data', app()->getLocale())->with('success', 'Cost center saved successfully');
    }

    public function storeParty(Request $request)
    {
        $data = $request->validate([
            'party_type' => ['required', 'in:customer,supplier,both'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'tax_number' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $party = FinanceParty::query()->create($data + [
            'opening_balance' => (float) ($data['opening_balance'] ?? 0),
            'is_active' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Party saved successfully',
                'party' => [
                    'id' => $party->id,
                    'name' => $party->name,
                    'party_type' => $party->party_type,
                    'phone' => $party->phone,
                    'email' => $party->email,
                    'tax_number' => $party->tax_number,
                ],
            ], 201);
        }

        return redirect()->route('admin.finance.master-data', app()->getLocale())->with('success', 'Party saved successfully');
    }

    public function vouchers(Request $request)
    {
        $vouchers = FinanceVoucher::query()
            ->with(['branch', 'costCenter', 'party', 'invoice'])
            ->when($request->filled('voucher_type'), fn ($q) => $q->where('voucher_type', $request->string('voucher_type')))
            ->when($request->filled('party_id'), fn ($q) => $q->where('party_id', (int) $request->input('party_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest('voucher_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $parties = FinanceParty::query()->where('is_active', true)->orderBy('name')->get();
        $invoices = FinanceInvoice::query()->whereIn('status', ['issued', 'partially_paid'])->orderByDesc('id')->get();
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $costCenters = FinanceCostCenter::query()->where('is_active', true)->orderBy('code')->get();

        $stats = [
            'receipts' => (float) FinanceVoucher::query()->where('status', 'posted')->where('voucher_type', 'receipt')->sum('amount'),
            'payments' => (float) FinanceVoucher::query()->where('status', 'posted')->where('voucher_type', 'payment')->sum('amount'),
            'open_linked' => FinanceVoucher::query()->whereNotNull('invoice_id')->where('status', 'posted')->count(),
        ];

        return view('admin.finance.vouchers', compact('vouchers', 'parties', 'invoices', 'branches', 'costCenters', 'stats'));
    }

    public function storeVoucher(Request $request)
    {
        $data = $request->validate([
            'voucher_type' => ['required', 'in:receipt,payment'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'cost_center_id' => ['nullable', 'exists:finance_cost_centers,id'],
            'party_id' => ['nullable', 'exists:finance_parties,id'],
            'invoice_id' => ['nullable', 'exists:finance_invoices,id'],
            'voucher_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $voucher = FinanceVoucher::query()->create([
                ...$data,
                'voucher_no' => 'VCH-' . now()->format('YmdHis'),
                'created_by' => auth()->id(),
                'status' => 'posted',
            ]);

            $this->operations->syncVoucher($voucher);
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['voucher_date' => $exception->getMessage()]);
        }

        return redirect()->route('admin.finance.vouchers', app()->getLocale())->with('success', 'Voucher posted successfully');
    }

    public function invoices(Request $request)
    {
        $invoices = FinanceInvoice::query()
            ->with(['branch', 'costCenter', 'party', 'items', 'vouchers'])
            ->when($request->filled('invoice_type'), fn ($q) => $q->where('invoice_type', $request->string('invoice_type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('party_id'), fn ($q) => $q->where('party_id', (int) $request->input('party_id')))
            ->latest('issue_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $parties = FinanceParty::query()->where('is_active', true)->orderBy('name')->get();
        $stats = [
            'customer' => (float) FinanceInvoice::query()->where('invoice_type', 'customer')->sum('total'),
            'supplier' => (float) FinanceInvoice::query()->where('invoice_type', 'supplier')->sum('total'),
            'receivable' => (float) FinanceInvoice::query()->where('invoice_type', 'customer')->sum('balance_due'),
            'payable' => (float) FinanceInvoice::query()->where('invoice_type', 'supplier')->sum('balance_due'),
        ];

        return view('admin.finance.invoices-index', compact('invoices', 'parties', 'stats'));
    }

    public function createInvoice()
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $costCenters = FinanceCostCenter::query()->where('is_active', true)->orderBy('code')->get();
        $parties = FinanceParty::query()->where('is_active', true)->orderBy('name')->get();
        $invoiceSettings = $this->invoiceSettings();

        return view('admin.finance.invoices-create', compact('branches', 'costCenters', 'parties', 'invoiceSettings'));
    }

    public function storeInvoice(Request $request)
    {
        $data = $request->validate([
            'invoice_type' => ['required', 'in:customer,supplier'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'cost_center_id' => ['nullable', 'exists:finance_cost_centers,id'],
            'party_id' => ['required', 'exists:finance_parties,id'],
            'invoice_scope' => ['required', 'in:simplified,standard'],
            'reference_number' => ['nullable', 'string', 'max:150'],
            'currency_code' => ['required', 'string', 'max:10'],
            'issue_date' => ['required', 'date'],
            'supply_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'payment_terms' => ['required', 'in:cash,credit'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
        ]);

        $items = collect($data['items'])
            ->filter(fn ($item) => filled($item['description'] ?? null))
            ->values();

        if ($items->isEmpty()) {
            return back()->withInput()->withErrors(['items' => 'At least one invoice item is required.']);
        }

        try {
            $invoice = FinanceInvoice::query()->create([
                'invoice_type' => $data['invoice_type'],
                'branch_id' => $data['branch_id'] ?? null,
                'cost_center_id' => $data['cost_center_id'] ?? null,
                'party_id' => $data['party_id'],
                'invoice_no' => 'INV-TMP-' . now()->format('YmdHis'),
                'uuid' => (string) Str::uuid(),
                'currency_code' => strtoupper((string) $data['currency_code']),
                'issue_date' => $data['issue_date'],
                'supply_date' => $data['supply_date'] ?? $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'payment_terms' => $data['payment_terms'],
                'invoice_scope' => $data['invoice_scope'],
                'reference_number' => $data['reference_number'] ?? null,
                'status' => 'issued',
                'zatca_status' => 'draft',
                'subtotal' => 0,
                'discount' => (float) ($data['discount'] ?? 0),
                'tax_rate' => (float) ($data['tax_rate'] ?? 15),
                'tax' => 0,
                'total' => 0,
                'paid_amount' => 0,
                'balance_due' => 0,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0;
            foreach ($items as $item) {
                if (! is_numeric($item['quantity'] ?? null) || (float) $item['quantity'] <= 0) {
                    return back()->withInput()->withErrors(['items' => 'Invoice quantities must be greater than zero.']);
                }

                if (! is_numeric($item['unit_price'] ?? null) || (float) $item['unit_price'] < 0) {
                    return back()->withInput()->withErrors(['items' => 'Invoice prices must be valid numbers.']);
                }

                $lineTotal = (float) $item['quantity'] * (float) $item['unit_price'];
                $subtotal += $lineTotal;
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $lineTotal,
                ]);
            }

            $taxableAmount = max($subtotal - (float) ($data['discount'] ?? 0), 0);
            $taxAmount = round($taxableAmount * ((float) ($data['tax_rate'] ?? 15) / 100), 2);
            $total = max($taxableAmount + $taxAmount, 0);
            $invoice->forceFill([
                'invoice_no' => $this->generateInvoiceNumber($invoice),
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'total' => $total,
                'balance_due' => $total,
            ])->save();

            $this->operations->syncInvoice($invoice);
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['issue_date' => $exception->getMessage()]);
        }

        return redirect()->route('admin.finance.invoices.show', [app()->getLocale(), $invoice])->with('success', 'Invoice saved successfully');
    }

    public function showInvoice(FinanceInvoice $invoice)
    {
        $invoice->load(['branch', 'costCenter', 'party', 'items', 'vouchers', 'creator']);

        $invoiceSettings = $this->invoiceSettings();
        $sellerName = $invoiceSettings['seller_name'];
        $sellerVatNumber = $invoiceSettings['seller_vat_number'];
        $barcodeSvg = Code39Barcode::toSvg($invoice->invoice_no, 74);
        $zatcaQrPayload = $sellerVatNumber !== ''
            ? ZatcaInvoiceQr::buildBase64(
                $sellerName,
                $sellerVatNumber,
                optional($invoice->issue_date)->format('Y-m-d\T00:00:00'),
                (float) $invoice->total,
                (float) $invoice->tax
            )
            : null;
        $zatcaQrUrl = $zatcaQrPayload ? ZatcaInvoiceQr::qrUrl($zatcaQrPayload) : null;

        $zatcaValidation = $invoice->zatca_validation ?? $this->zatcaValidator->validate($invoice);

        return view('admin.finance.invoices-show', compact('invoice', 'invoiceSettings', 'barcodeSvg', 'zatcaQrPayload', 'zatcaQrUrl', 'zatcaValidation'));
    }

    public function downloadInvoicePdf(FinanceInvoice $invoice)
    {
        $invoice->load(['branch', 'costCenter', 'party', 'items', 'vouchers', 'creator']);

        $invoiceSettings = $this->invoiceSettings();
        $sellerName = $invoiceSettings['seller_name'];
        $sellerVatNumber = $invoiceSettings['seller_vat_number'];
        $browserBinary = $this->detectPdfBrowserBinary();
        $supportsPdfQr = extension_loaded('gd');
        $supportsBrowserQr = (bool) $browserBinary;
        $barcodeSvg = Code39Barcode::toSvg($invoice->invoice_no, 74);
        $zatcaQrPayload = $sellerVatNumber !== ''
            ? ZatcaInvoiceQr::buildBase64(
                $sellerName,
                $sellerVatNumber,
                optional($invoice->issue_date)->format('Y-m-d\T00:00:00'),
                (float) $invoice->total,
                (float) $invoice->tax
            )
            : null;
        $zatcaQrUrl = (($supportsPdfQr || $supportsBrowserQr) && $zatcaQrPayload)
            ? ZatcaInvoiceQr::qrUrl($zatcaQrPayload)
            : null;
        $zatcaValidation = $invoice->zatca_validation ?? $this->zatcaValidator->validate($invoice);

        $viewData = compact(
            'invoice',
            'invoiceSettings',
            'barcodeSvg',
            'supportsPdfQr',
            'supportsBrowserQr',
            'zatcaQrPayload',
            'zatcaQrUrl',
            'zatcaValidation'
        );

        $browserPdf = $this->renderInvoicePdfWithBrowser($viewData, $browserBinary);
        if ($browserPdf) {
            return response()->download($browserPdf, $invoice->invoice_no . '.pdf')->deleteFileAfterSend(true);
        }

        $pdf = Pdf::loadView('pdf.finance-invoice', compact(
            'invoice',
            'invoiceSettings',
            'barcodeSvg',
            'supportsPdfQr',
            'supportsBrowserQr',
            'zatcaQrPayload',
            'zatcaQrUrl',
            'zatcaValidation'
        ))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isRemoteEnabled' => $supportsPdfQr,
                'isHtml5ParserEnabled' => true,
                'dpi' => 120,
                'defaultFont' => 'DejaVu Sans',
                'chroot' => base_path(),
            ]);

        return $pdf->download($invoice->invoice_no . '.pdf');
    }

    public function generateZatca(FinanceInvoice $invoice)
    {
        $this->zatcaWorkflow->generate($invoice);

        return redirect()
            ->route('admin.finance.invoices.show', [app()->getLocale(), $invoice])
            ->with('success', 'ZATCA XML generated successfully');
    }

    public function validateZatca(FinanceInvoice $invoice)
    {
        $invoice = $this->zatcaWorkflow->generate($invoice);

        $validation = $invoice->zatca_validation ?? ['valid' => false, 'errors' => ['Validation data unavailable'], 'warnings' => []];
        $message = ($validation['valid'] ?? false)
            ? 'Invoice passed local ZATCA validation'
            : ('Invoice validation failed: ' . implode(' | ', $validation['errors'] ?? []));

        return redirect()
            ->route('admin.finance.invoices.show', [app()->getLocale(), $invoice])
            ->with(($validation['valid'] ?? false) ? 'success' : 'error', $message);
    }

    public function submitZatca(FinanceInvoice $invoice)
    {
        try {
            $invoice = $this->zatcaWorkflow->submit($invoice);
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.finance.invoices.show', [app()->getLocale(), $invoice])
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.finance.invoices.show', [app()->getLocale(), $invoice])
            ->with('success', 'Invoice submitted to ZATCA workflow');
    }

    public function downloadZatcaXml(FinanceInvoice $invoice)
    {
        if (! $invoice->zatca_xml_path || ! Storage::disk('public')->exists($invoice->zatca_xml_path)) {
            $invoice = $this->zatcaWorkflow->generate($invoice);
        }

        return Storage::disk('public')->download($invoice->zatca_xml_path, $invoice->invoice_no . '.xml');
    }

    public function reports(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $statementLines = AccountingJournalLine::query()
            ->with('account')
            ->whereHas('journal', fn ($q) => $q->where('status', 'posted')->whereBetween('journal_date', [$dateFrom, $dateTo]))
            ->get()
            ->groupBy('account.category');

        $incomeStatement = [
            'revenues' => $this->sumCategory($statementLines->get('revenue')),
            'expenses' => $this->sumCategory($statementLines->get('expense')),
        ];
        $incomeStatement['net_profit'] = $incomeStatement['revenues'] - $incomeStatement['expenses'];

        $balanceSheetDate = $request->input('balance_date', $dateTo);
        $balanceSheet = [
            'assets' => $this->balancesByCategory('asset', $balanceSheetDate),
            'liabilities' => $this->balancesByCategory('liability', $balanceSheetDate),
            'equity' => $this->balancesByCategory('equity', $balanceSheetDate),
        ];

        $aging = [
            'receivables' => $this->agingBuckets('customer'),
            'payables' => $this->agingBuckets('supplier'),
        ];

        $closings = AccountingPeriodClosing::query()->latest('year')->latest('month')->paginate(12, ['*'], 'closings_page');

        return view('admin.finance.reports', compact('dateFrom', 'dateTo', 'balanceSheetDate', 'incomeStatement', 'balanceSheet', 'aging', 'closings'));
    }

    public function storeClosing(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'notes' => ['nullable', 'string'],
        ]);

        $start = now()->setDate($data['year'], $data['month'], 1)->startOfMonth()->toDateString();
        $end = now()->setDate($data['year'], $data['month'], 1)->endOfMonth()->toDateString();

        $revenues = $this->sumCategory(
            AccountingJournalLine::query()
                ->with('account')
                ->whereHas('journal', fn ($q) => $q->where('status', 'posted')->whereBetween('journal_date', [$start, $end]))
                ->get()
                ->where('account.category', 'revenue')
        );
        $expenses = $this->sumCategory(
            AccountingJournalLine::query()
                ->with('account')
                ->whereHas('journal', fn ($q) => $q->where('status', 'posted')->whereBetween('journal_date', [$start, $end]))
                ->get()
                ->where('account.category', 'expense')
        );

        AccountingPeriodClosing::query()->updateOrCreate(
            ['period_key' => sprintf('%04d-%02d', $data['year'], $data['month'])],
            [
                'year' => $data['year'],
                'month' => $data['month'],
                'status' => 'closed',
                'income_total' => $revenues,
                'expense_total' => $expenses,
                'net_profit' => $revenues - $expenses,
                'closed_at' => now(),
                'closed_by' => auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]
        );

        return redirect()->route('admin.finance.reports', app()->getLocale())->with('success', 'Monthly closing saved successfully');
    }

    public function inventory()
    {
        $branches = Branch::query()->where('is_active', true)->orderBy('sort_order')->get();
        $warehouses = InventoryWarehouse::query()->with('branch')->latest()->get();
        $items = InventoryItem::query()->with('warehouse')->latest()->paginate(12, ['*'], 'items_page');
        $movements = InventoryMovement::query()->with(['warehouse', 'item', 'branch'])->latest('movement_date')->latest('id')->paginate(12, ['*'], 'movements_page');

        $summary = [
            'warehouses' => $warehouses->count(),
            'items' => InventoryItem::query()->count(),
            'low_stock' => InventoryItem::query()->whereColumn('current_stock', '<=', 'min_stock')->count(),
            'stock_value' => (float) InventoryItem::query()->get()->sum(fn ($item) => (float) $item->current_stock * (float) $item->average_cost),
        ];

        return view('admin.finance.inventory', compact('branches', 'warehouses', 'items', 'movements', 'summary'));
    }

    public function storeWarehouse(Request $request)
    {
        $data = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'code' => ['required', 'string', 'max:50', 'unique:inventory_warehouses,code'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        InventoryWarehouse::query()->create($data + ['is_active' => true]);

        return redirect()->route('admin.finance.inventory', app()->getLocale())->with('success', 'Warehouse saved successfully');
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => ['nullable', 'exists:inventory_warehouses,id'],
            'item_code' => ['required', 'string', 'max:50', 'unique:inventory_items,item_code'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:tool,supply,medicine,other'],
            'unit' => ['required', 'string', 'max:50'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'average_cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        InventoryItem::query()->create($data + [
            'min_stock' => (float) ($data['min_stock'] ?? 0),
            'current_stock' => 0,
            'average_cost' => (float) ($data['average_cost'] ?? 0),
            'is_active' => true,
        ]);

        return redirect()->route('admin.finance.inventory', app()->getLocale())->with('success', 'Inventory item saved successfully');
    }

    public function storeMovement(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => ['required', 'exists:inventory_warehouses,id'],
            'item_id' => ['required', 'exists:inventory_items,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'movement_type' => ['required', 'in:receipt,issue,adjustment_in,adjustment_out'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'movement_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->operations->assertPeriodOpen($data['movement_date']);

            $movement = InventoryMovement::query()->create([
                ...$data,
                'unit_cost' => (float) ($data['unit_cost'] ?? 0),
                'total_cost' => (float) $data['quantity'] * (float) ($data['unit_cost'] ?? 0),
                'created_by' => auth()->id(),
            ]);

            $this->operations->applyInventoryMovement($movement);
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['movement_date' => $exception->getMessage()]);
        }

        return redirect()->route('admin.finance.inventory', app()->getLocale())->with('success', 'Inventory movement saved successfully');
    }

    private function sumCategory($lines): float
    {
        return (float) collect($lines)->sum(function ($line) {
            $normalBalance = $line->account?->normal_balance;
            return $normalBalance === 'credit'
                ? ((float) $line->credit - (float) $line->debit)
                : ((float) $line->debit - (float) $line->credit);
        });
    }

    private function balancesByCategory(string $category, string $date): array
    {
        return AccountingAccount::query()
            ->where('category', $category)
            ->with(['lines' => fn ($q) => $q->whereHas('journal', fn ($jq) => $jq->where('status', 'posted')->whereDate('journal_date', '<=', $date))])
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                $debit = (float) $account->lines->sum('debit');
                $credit = (float) $account->lines->sum('credit');
                $balance = $account->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'balance' => $balance,
                ];
            })
            ->filter(fn ($row) => abs((float) $row['balance']) > 0.0001)
            ->values()
            ->all();
    }

    private function agingBuckets(string $type): array
    {
        $invoices = FinanceInvoice::query()
            ->with('party')
            ->where('invoice_type', $type)
            ->where('balance_due', '>', 0)
            ->whereIn('status', ['issued', 'partially_paid'])
            ->get();

        $rows = [];

        foreach ($invoices as $invoice) {
            $days = $invoice->due_date
                ? max($invoice->due_date->copy()->startOfDay()->diffInDays(now()->startOfDay(), false), 0)
                : 0;
            $bucket = match (true) {
                $days <= 30 => '0-30',
                $days <= 60 => '31-60',
                $days <= 90 => '61-90',
                default => '90+',
            };

            $rows[] = [
                'invoice_no' => $invoice->invoice_no,
                'party' => $invoice->party?->name ?? '-',
                'due_date' => optional($invoice->due_date)->format('Y-m-d') ?: '-',
                'balance_due' => (float) $invoice->balance_due,
                'bucket' => $bucket,
            ];
        }

        return $rows;
    }

    private function generateInvoiceNumber(FinanceInvoice $invoice): string
    {
        return sprintf('INV-%s-%05d', $invoice->issue_date->format('Y'), $invoice->id);
    }

    private function invoiceSettings(): array
    {
        $isAr = app()->getLocale() === 'ar';

        return [
            'seller_name' => (string) Setting::getValue('site_name', 'Dr Halim Dental'),
            'seller_vat_number' => (string) Setting::getValue('seller_vat_number', ''),
            'seller_cr_number' => (string) Setting::getValue('seller_cr_number', ''),
            'seller_address' => (string) Setting::getValue($isAr ? 'seller_address_ar' : 'seller_address_en', (string) Setting::getValue('site_city', '')),
            'seller_phone' => (string) Setting::getValue('site_phone', ''),
            'seller_email' => (string) Setting::getValue('site_email', ''),
            'footer_note' => (string) Setting::getValue($isAr ? 'invoice_footer_note_ar' : 'invoice_footer_note_en', ''),
        ];
    }

    private function renderInvoicePdfWithBrowser(array $viewData, ?string $browserBinary = null): ?string
    {
        if (! $browserBinary || ! function_exists('proc_open')) {
            return null;
        }

        $tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'dr_halim_invoice_pdf';
        File::ensureDirectoryExists($tmpDir);

        $token = Str::uuid()->toString();
        $htmlPath = $tmpDir . DIRECTORY_SEPARATOR . 'invoice_' . $token . '.html';
        $pdfPath = $tmpDir . DIRECTORY_SEPARATOR . 'invoice_' . $token . '.pdf';

        try {
            $html = view('pdf.finance-invoice', $viewData + ['renderMode' => 'browser'])->render();
            File::put($htmlPath, $html);

            $fileUrl = $this->fileUrlForBrowser($htmlPath);
            $command = '"' . $browserBinary . '" --headless=new --disable-gpu --allow-file-access-from-files --virtual-time-budget=4000 --print-to-pdf="' . $pdfPath . '" --print-to-pdf-no-header "' . $fileUrl . '"';

            $process = proc_open(
                $command,
                [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ],
                $pipes
            );

            if (! is_resource($process)) {
                return null;
            }

            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

            File::delete($htmlPath);

            if ($exitCode !== 0 || ! File::exists($pdfPath) || File::size($pdfPath) === 0) {
                logger()->warning('Browser PDF generation failed', [
                    'stdout' => $stdout,
                    'stderr' => $stderr,
                    'exit_code' => $exitCode,
                ]);

                File::delete($pdfPath);

                return null;
            }

            return $pdfPath;
        } catch (\Throwable $e) {
            File::delete([$htmlPath, $pdfPath]);
            logger()->warning('Browser PDF generation exception', ['message' => $e->getMessage()]);

            return null;
        }
    }

    private function detectPdfBrowserBinary(): ?string
    {
        $candidates = [
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
            'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
            'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function fileUrlForBrowser(string $path): string
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', realpath($path) ?: $path);
        $segments = array_map('rawurlencode', explode('/', $path));

        return 'file:///' . implode('/', $segments);
    }
}
