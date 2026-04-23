@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'فواتير العملاء والموردين' : 'Customer & Supplier Invoices' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'إدارة الذمم المدينة والدائنة وربطها بالسندات والقيود مع صفحة عرض احترافية لكل فاتورة.' : 'Manage receivables and payables linked to vouchers and accounting journals, with a professional invoice view for each document.' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-outline-secondary" href="{{ route('admin.finance.vouchers', app()->getLocale()) }}">{{ $isAr ? 'السندات' : 'Vouchers' }}</a>
            <a class="btn btn-primary" href="{{ route('admin.finance.invoices.create', app()->getLocale()) }}">{{ $isAr ? 'إنشاء فاتورة' : 'Create Invoice' }}</a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-panel"><small>{{ $isAr ? 'مبيعات العملاء' : 'Customer Sales' }}</small><strong>{{ number_format($stats['customer'], 2) }}</strong><span>{{ $isAr ? 'إجمالي فواتير العملاء' : 'Total customer invoices' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'فواتير الموردين' : 'Supplier Bills' }}</small><strong>{{ number_format($stats['supplier'], 2) }}</strong><span>{{ $isAr ? 'إجمالي فواتير الموردين' : 'Total supplier invoices' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'ذمم مدينة' : 'A/R Balance' }}</small><strong>{{ number_format($stats['receivable'], 2) }}</strong><span>{{ $isAr ? 'المبالغ المستحقة من العملاء' : 'Outstanding customer balances' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'ذمم دائنة' : 'A/P Balance' }}</small><strong>{{ number_format($stats['payable'], 2) }}</strong><span>{{ $isAr ? 'المبالغ المستحقة للموردين' : 'Outstanding supplier balances' }}</span></div>
    </div>

    <div class="admin-filter-card">
        <form class="row g-2" method="GET" action="{{ route('admin.finance.invoices', app()->getLocale()) }}">
            <div class="col-md-4">
                <select class="form-select" name="invoice_type">
                    <option value="">{{ $isAr ? 'كل الأنواع' : 'All types' }}</option>
                    <option value="customer" @selected(request('invoice_type') === 'customer')>{{ $isAr ? 'عملاء' : 'Customer' }}</option>
                    <option value="supplier" @selected(request('invoice_type') === 'supplier')>{{ $isAr ? 'موردون' : 'Supplier' }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="party_id">
                    <option value="">{{ $isAr ? 'كل الأطراف' : 'All parties' }}</option>
                    @foreach($parties as $party)
                        <option value="{{ $party->id }}" @selected((string) request('party_id') === (string) $party->id)>{{ $party->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">{{ $isAr ? 'كل الحالات' : 'All statuses' }}</option>
                    @foreach(['issued','partially_paid','paid','cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100">{{ $isAr ? 'تصفية' : 'Filter' }}</button>
                <a class="btn btn-outline-secondary w-100" href="{{ route('admin.finance.invoices', app()->getLocale()) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a>
            </div>
        </form>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الفاتورة' : 'Invoice' }}</th>
                    <th>{{ $isAr ? 'الطرف' : 'Party' }}</th>
                    <th>{{ $isAr ? 'النطاق / العملة' : 'Scope / Currency' }}</th>
                    <th>{{ $isAr ? 'التواريخ' : 'Dates' }}</th>
                    <th>{{ $isAr ? 'الإجمالي' : 'Total' }}</th>
                    <th>{{ $isAr ? 'المسدد' : 'Paid' }}</th>
                    <th>{{ $isAr ? 'المتبقي' : 'Balance' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'إجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $invoice->invoice_no }}</div>
                            <small class="text-muted">{{ $invoice->invoice_type }} | {{ $invoice->payment_terms }}</small>
                        </td>
                        <td>
                            <div>{{ $invoice->party?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $invoice->party?->tax_number ?? '-' }}</small>
                        </td>
                        <td>
                            <div>{{ $invoice->invoice_scope ?? 'simplified' }}</div>
                            <small class="text-muted">{{ strtoupper($invoice->currency_code ?? 'SAR') }}</small>
                        </td>
                        <td>
                            <div>{{ optional($invoice->issue_date)->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ optional($invoice->due_date)->format('Y-m-d') ?: '-' }}</small>
                        </td>
                        <td>{{ number_format((float) $invoice->total, 2) }}</td>
                        <td class="text-success">{{ number_format((float) $invoice->paid_amount, 2) }}</td>
                        <td class="{{ (float) $invoice->balance_due > 0 ? 'text-danger' : 'text-success' }}">{{ number_format((float) $invoice->balance_due, 2) }}</td>
                        <td><span class="admin-status-pill {{ $invoice->status === 'paid' ? '' : 'is-muted' }}">{{ $invoice->status }}</span></td>
                        <td>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.finance.invoices.show', [app()->getLocale(), $invoice]) }}">{{ $isAr ? 'عرض' : 'View' }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="admin-empty">{{ $isAr ? 'لا توجد فواتير' : 'No invoices found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $invoices->links() }}</div>
</div>
@endsection
