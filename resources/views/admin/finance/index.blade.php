@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $pageIncome = $entries->getCollection()->where('entry_type', 'income')->where('record_status', 'posted')->sum('amount');
    $pageExpense = $entries->getCollection()->where('entry_type', 'expense')->where('record_status', 'posted')->sum('amount');
    $pageNet = $pageIncome - $pageExpense;
@endphp

<style>
    .finance-hub{display:grid;gap:1rem}
    .finance-hub .hero-card,.finance-hub .panel-card{border:1px solid #d7ebe4;border-radius:1.6rem;background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(245,252,249,.96) 100%);box-shadow:0 18px 38px rgba(16,82,92,.08);padding:1rem 1.1rem}
    .finance-hub .hero-row,.finance-hub .panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
    .finance-hub .hero-kicker{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:999px;background:#ebfaf5;border:1px solid #d6ece5;color:#13656b;font-size:.78rem;font-weight:800}
    .finance-hub .hero-title{margin:.55rem 0 .3rem;color:#18485d;font-size:clamp(1.7rem,1.35rem + .7vw,2.3rem);font-weight:900}
    .finance-hub .hero-copy,.finance-hub .panel-subtitle{margin:0;color:#69808b;line-height:1.8}
    .finance-hub .hero-actions,.finance-hub .quick-links{display:flex;gap:.6rem;flex-wrap:wrap}
    .finance-hub .stats-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:.8rem}
    .finance-hub .stat-card{padding:.9rem;border:1px solid #d7ebe4;border-radius:1rem;background:#fff}
    .finance-hub .stat-card small{display:block;color:#68808b;font-weight:700;margin-bottom:.2rem}
    .finance-hub .stat-card strong{display:block;font-size:1.2rem;line-height:1.15}
    .finance-hub .filter-grid{display:grid;grid-template-columns:2fr repeat(8,minmax(0,1fr));gap:.75rem}
    .finance-hub .table-shell{border:1px solid #deede7;border-radius:1.15rem;overflow:hidden;background:#fff}
    .finance-hub .table thead th{background:#edf9f4;color:#285a66;font-size:.82rem;font-weight:800;border-bottom:1px solid #d8ebe4}
    .finance-hub .summary-note{color:#6e858e}
    @media (max-width:1399.98px){.finance-hub .stats-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.finance-hub .filter-grid{grid-template-columns:repeat(3,minmax(0,1fr))}.finance-hub .filter-grid .wide{grid-column:1/-1}}
    @media (max-width:767.98px){.finance-hub .stats-grid,.finance-hub .filter-grid{grid-template-columns:1fr}}
</style>

<div class="finance-hub">
    <section class="hero-card">
        <div class="hero-row">
            <div>
                <span class="hero-kicker"><i class="bi bi-bank"></i> {{ $isAr ? 'المركز المالي' : 'Finance hub' }}</span>
                <h1 class="hero-title">{{ $isAr ? 'مركز المالية والمحاسبة' : 'Finance & Accounting Hub' }}</h1>
                <p class="hero-copy">{{ $isAr ? 'واجهة موحدة للقيود اليومية والذمم والتقارير والمخزون مع وصول أسرع للشاشات التشغيلية.' : 'Unified workspace for journals, receivables, reports, and inventory operations.' }}</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-outline-dark" href="{{ route('admin.finance.cashbox', app()->getLocale()) }}">{{ $isAr ? 'الخزنة' : 'Cashbox' }}</a>
                <a class="btn btn-outline-dark" href="{{ route('admin.finance.accounting', app()->getLocale()) }}">{{ $isAr ? 'المحاسبة العامة' : 'General accounting' }}</a>
                <a class="btn btn-primary" href="{{ route('admin.finance.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة قيد مالي' : 'Add entry' }}</a>
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="quick-links">
            <a class="btn btn-outline-dark" href="{{ route('admin.finance.master-data', app()->getLocale()) }}">{{ $isAr ? 'البيانات المرجعية' : 'Master data' }}</a>
            <a class="btn btn-outline-dark" href="{{ route('admin.finance.invoices', app()->getLocale()) }}">{{ $isAr ? 'الفواتير والذمم' : 'Invoices & AR/AP' }}</a>
            <a class="btn btn-outline-dark" href="{{ route('admin.finance.vouchers', app()->getLocale()) }}">{{ $isAr ? 'السندات' : 'Vouchers' }}</a>
            <a class="btn btn-outline-dark" href="{{ route('admin.finance.reports', app()->getLocale()) }}">{{ $isAr ? 'التقارير والإقفال' : 'Reports & closing' }}</a>
            <a class="btn btn-outline-dark" href="{{ route('admin.finance.inventory', app()->getLocale()) }}">{{ $isAr ? 'المخزون والمستودع' : 'Inventory & warehouse' }}</a>
            <a class="btn btn-outline-success" href="{{ route('admin.finance.export', array_merge([app()->getLocale()], request()->query())) }}">{{ $isAr ? 'تصدير CSV' : 'Export CSV' }}</a>
            <a class="btn btn-success" href="{{ route('admin.finance.export-xlsx', array_merge([app()->getLocale()], request()->query())) }}">Excel .xlsx</a>
            <a class="btn btn-outline-danger" href="{{ route('admin.finance.report-pdf', array_merge([app()->getLocale()], request()->query(), ['period' => 'day'])) }}">{{ $isAr ? 'PDF يومي' : 'Daily PDF' }}</a>
            <a class="btn btn-outline-danger" href="{{ route('admin.finance.report-pdf', array_merge([app()->getLocale()], request()->query(), ['period' => 'month'])) }}">{{ $isAr ? 'PDF شهري' : 'Monthly PDF' }}</a>
            <form method="POST" action="{{ route('admin.finance.sync-appointments', app()->getLocale()) }}">
                @csrf
                <button class="btn btn-outline-primary">{{ $isAr ? 'مزامنة حجوزات قديمة' : 'Sync old appointments' }}</button>
            </form>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-card"><small>{{ $isAr ? 'إيراد مفلتر' : 'Filtered income' }}</small><strong class="text-success">{{ number_format($statsFiltered['income_total'] ?? 0, 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'مصروف مفلتر' : 'Filtered expense' }}</small><strong class="text-danger">{{ number_format($statsFiltered['expense_total'] ?? 0, 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'صافي مفلتر' : 'Filtered net' }}</small><strong class="{{ ($statsFiltered['net'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($statsFiltered['net'] ?? 0, 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'إيراد حجوزات' : 'Appointments income' }}</small><strong>{{ number_format($statsFiltered['appointment_income'] ?? 0, 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'فواتير داخلة' : 'Incoming invoices' }}</small><strong>{{ number_format($statsFiltered['incoming_invoices'] ?? 0, 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'فواتير خارجة' : 'Outgoing invoices' }}</small><strong>{{ number_format($statsFiltered['outgoing_invoices'] ?? 0, 2) }}</strong></div>
    </section>

    <section class="panel-card">
        <div class="panel-head mb-3">
            <div>
                <h2 class="panel-title">{{ $isAr ? 'فلترة القيود' : 'Entry filters' }}</h2>
                <p class="panel-subtitle">{{ $isAr ? 'ابحث وفلتر القيود اليومية حسب النوع والبند والحالة والتاريخ.' : 'Search and filter journal entries by type, kind, status, and date.' }}</p>
            </div>
            <div class="summary-note">{{ $isAr ? 'عدد النتائج الحالية:' : 'Current results:' }} <strong>{{ $entries->total() }}</strong></div>
        </div>
        <form class="filter-grid" method="GET" action="{{ route('admin.finance.index', app()->getLocale()) }}">
            <div class="wide"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="{{ $isAr ? 'بحث بعنوان/فاتورة/جهة/ملاحظة' : 'Search title/invoice/counterparty/notes' }}"></div>
            <div><select class="form-select" name="entry_type"><option value="">{{ $isAr ? 'كل الأنواع' : 'All types' }}</option><option value="income" @selected(request('entry_type') === 'income')>{{ $isAr ? 'إيراد' : 'Income' }}</option><option value="expense" @selected(request('entry_type') === 'expense')>{{ $isAr ? 'مصروف' : 'Expense' }}</option></select></div>
            <div><select class="form-select" name="entry_kind"><option value="">{{ $isAr ? 'كل البنود' : 'All kinds' }}</option><option value="appointment" @selected(request('entry_kind') === 'appointment')>{{ $isAr ? 'حجز' : 'Appointment' }}</option><option value="incoming_invoice" @selected(request('entry_kind') === 'incoming_invoice')>{{ $isAr ? 'فاتورة داخلة' : 'Incoming invoice' }}</option><option value="outgoing_invoice" @selected(request('entry_kind') === 'outgoing_invoice')>{{ $isAr ? 'فاتورة خارجة' : 'Outgoing invoice' }}</option><option value="expense" @selected(request('entry_kind') === 'expense')>{{ $isAr ? 'مصروف تشغيلي' : 'Operational expense' }}</option><option value="other" @selected(request('entry_kind') === 'other')>{{ $isAr ? 'أخرى' : 'Other' }}</option></select></div>
            <div><select class="form-select" name="record_status"><option value="">{{ $isAr ? 'كل الحالات' : 'All statuses' }}</option><option value="posted" @selected(request('record_status') === 'posted')>{{ $isAr ? 'مرحل' : 'Posted' }}</option><option value="void" @selected(request('record_status') === 'void')>{{ $isAr ? 'ملغي' : 'Void' }}</option></select></div>
            <div><select class="form-select" name="branch_id"><option value="">{{ $isAr ? 'كل الفروع' : 'All branches' }}</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>@endforeach</select></div>
            <div><input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}"></div>
            <div><input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}"></div>
            <div><select class="form-select" name="sort_by"><option value="entry_date" @selected(request('sort_by', 'entry_date') === 'entry_date')>{{ $isAr ? 'ترتيب بالتاريخ' : 'Sort by date' }}</option><option value="amount" @selected(request('sort_by') === 'amount')>{{ $isAr ? 'ترتيب بالمبلغ' : 'Sort by amount' }}</option><option value="id" @selected(request('sort_by') === 'id')>{{ $isAr ? 'ترتيب بالرقم' : 'Sort by id' }}</option></select></div>
            <div><select class="form-select" name="sort_dir"><option value="desc" @selected(request('sort_dir', 'desc') === 'desc')>{{ $isAr ? 'الأحدث' : 'Desc' }}</option><option value="asc" @selected(request('sort_dir') === 'asc')>{{ $isAr ? 'الأقدم' : 'Asc' }}</option></select></div>
            <div><button class="btn btn-primary w-100">{{ $isAr ? 'تطبيق' : 'Apply' }}</button></div>
        </form>
    </section>

    <section class="panel-card">
        <div class="panel-head mb-3">
            <div>
                <h2 class="panel-title">{{ $isAr ? 'القيود الحالية' : 'Current entries' }}</h2>
                <p class="panel-subtitle">{{ $isAr ? 'عرض أوضح للقيود مع صافي الصفحة الحالية في الأسفل.' : 'Cleaner current-entry table with page net at the bottom.' }}</p>
            </div>
        </div>
        <div class="table-shell">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ $isAr ? 'النوع' : 'Type' }}</th>
                            <th>{{ $isAr ? 'البند' : 'Kind' }}</th>
                            <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                            <th>{{ $isAr ? 'الفرع' : 'Branch' }}</th>
                            <th>{{ $isAr ? 'طريقة الدفع' : 'Payment' }}</th>
                            <th>{{ $isAr ? 'المبلغ' : 'Amount' }}</th>
                            <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr>
                                <td>{{ $entry->id }}</td>
                                <td>{{ optional($entry->entry_date)->format('Y-m-d') }}</td>
                                <td><span class="badge {{ $entry->entry_type === 'income' ? 'text-bg-success' : 'text-bg-danger' }}">{{ $entry->entry_type }}</span></td>
                                <td><span class="badge text-bg-light">{{ $entry->entry_kind }}</span></td>
                                <td><div class="fw-semibold">{{ $entry->title }}</div><small class="text-muted">{{ $entry->invoice_number ?: '-' }}@if($entry->counterparty) | {{ $entry->counterparty }}@endif</small></td>
                                <td>{{ $entry->branch?->name ?? '-' }}</td>
                                <td>{{ $entry->payment_method ?: '-' }}</td>
                                <td class="{{ $entry->entry_type === 'income' ? 'text-success' : 'text-danger' }}">{{ $entry->entry_type === 'income' ? '+' : '-' }} {{ number_format((float) $entry->amount, 2) }}</td>
                                <td><span class="badge {{ $entry->record_status === 'posted' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $entry->record_status }}</span></td>
                                <td class="text-nowrap">
                                    <a class="btn btn-sm btn-info" href="{{ route('admin.finance.show', [app()->getLocale(), $entry->id]) }}">{{ $isAr ? 'عرض' : 'View' }}</a>
                                    @if($entry->entry_kind !== 'appointment')
                                        <a class="btn btn-sm btn-warning" href="{{ route('admin.finance.edit', [app()->getLocale(), $entry->id]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                        <form method="POST" action="{{ route('admin.finance.destroy', [app()->getLocale(), $entry->id]) }}" class="d-inline" onsubmit="return confirm('{{ $isAr ? 'حذف القيد؟' : 'Delete entry?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-4">{{ $isAr ? 'لا توجد قيود' : 'No entries' }}</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr><th colspan="7" class="text-end">{{ $isAr ? 'صافي الصفحة الحالية' : 'Current page net' }}</th><th class="{{ $pageNet >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($pageNet, 2) }}</th><th colspan="2"></th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $entries->links() }}</div>
        <div class="mt-3 text-muted small">
            {{ $isAr ? 'إجماليات عامة (مرحل فقط):' : 'Global totals (posted only):' }}
            {{ $isAr ? 'الإيراد' : 'Income' }} {{ number_format($statsGlobal['income_total'] ?? 0, 2) }} |
            {{ $isAr ? 'المصروف' : 'Expense' }} {{ number_format($statsGlobal['expense_total'] ?? 0, 2) }} |
            {{ $isAr ? 'الصافي' : 'Net' }} {{ number_format($statsGlobal['net'] ?? 0, 2) }}
        </div>
    </section>
</div>
@endsection
