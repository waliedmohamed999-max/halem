@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .reports-workspace{display:grid;gap:1rem}
    .reports-workspace .hero-card,.reports-workspace .panel-card{border:1px solid #d7ebe4;border-radius:1.6rem;background:linear-gradient(180deg,rgba(255,255,255,.98) 0%,rgba(245,252,249,.96) 100%);box-shadow:0 18px 38px rgba(16,82,92,.08);padding:1rem 1.1rem}
    .reports-workspace .hero-row,.reports-workspace .panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
    .reports-workspace .hero-kicker{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .8rem;border-radius:999px;background:#ebfaf5;border:1px solid #d6ece5;color:#13656b;font-size:.78rem;font-weight:800}
    .reports-workspace .hero-title{margin:.55rem 0 .3rem;color:#18485d;font-size:clamp(1.7rem,1.35rem + .7vw,2.3rem);font-weight:900}
    .reports-workspace .hero-copy,.reports-workspace .panel-subtitle{margin:0;color:#69808b;line-height:1.8}
    .reports-workspace .filter-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.8rem}
    .reports-workspace .stats-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:.85rem}
    .reports-workspace .stat-card{padding:.95rem 1rem;border:1px solid #d7ebe4;border-radius:1.1rem;background:rgba(255,255,255,.9)}
    .reports-workspace .stat-card small{display:block;color:#68808b;font-weight:700;margin-bottom:.25rem}
    .reports-workspace .stat-card strong{display:block;color:#155f69;font-size:1.6rem;line-height:1;font-weight:900}
    .reports-workspace .workspace-grid{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(320px,.8fr);gap:1rem;align-items:start}
    .reports-workspace .stack{display:grid;gap:1rem}
    .reports-workspace .panel-title{margin:0;color:#18485d;font-size:1.14rem;font-weight:850}
    .reports-workspace .table-shell{border:1px solid #deede7;border-radius:1.15rem;overflow:hidden;background:#fff}
    .reports-workspace .table thead th{background:#edf9f4;color:#285a66;font-size:.82rem;font-weight:800;border-bottom:1px solid #d8ebe4}
    .reports-workspace .summary-list{display:grid;gap:.55rem}
    .reports-workspace .summary-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.62rem .75rem;border:1px solid #deede7;border-radius:.85rem;background:#fff}
    .reports-workspace .balance-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem}
    .reports-workspace .balance-card{border:1px solid #deede7;border-radius:1rem;padding:.9rem;background:#fff}
    .reports-workspace .balance-card h6{margin:0 0 .75rem;color:#18485d;font-weight:800}
    .reports-workspace .balance-row{display:flex;align-items:center;justify-content:space-between;gap:.75rem;padding:.35rem 0;border-bottom:1px dashed #e3efea}
    .reports-workspace .balance-row:last-child{border-bottom:0;padding-bottom:0}
    .form-label{display:block;margin-bottom:.35rem;color:#476471;font-size:.84rem;font-weight:800}
    @media (max-width:1199.98px){.reports-workspace .workspace-grid,.reports-workspace .balance-grid{grid-template-columns:1fr}.reports-workspace .filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:767.98px){.reports-workspace .filter-grid,.reports-workspace .stats-grid{grid-template-columns:1fr}}
</style>

<div class="reports-workspace">
    <section class="hero-card">
        <div class="hero-row">
            <div>
                <span class="hero-kicker"><i class="bi bi-bar-chart-line"></i> {{ $isAr ? 'القوائم والإقفال' : 'Statements & closing' }}</span>
                <h1 class="hero-title">{{ $isAr ? 'التقارير والإقفال الشهري' : 'Reports & Monthly Closing' }}</h1>
                <p class="hero-copy">{{ $isAr ? 'قائمة دخل وميزانية وأعمار ذمم مع مساحة أوضح لمتابعة الإقفال الشهري.' : 'Income statement, balance sheet, aging analysis, and monthly closing in a cleaner workspace.' }}</p>
            </div>
            <div>
                <a class="btn btn-outline-secondary" href="{{ route('admin.finance.accounting', app()->getLocale()) }}">{{ $isAr ? 'المحاسبة العامة' : 'General accounting' }}</a>
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-head mb-3">
            <div>
                <h2 class="panel-title">{{ $isAr ? 'فلاتر الفترة المالية' : 'Reporting period' }}</h2>
                <p class="panel-subtitle">{{ $isAr ? 'حدد فترة التقرير وتاريخ الميزانية قبل التحديث.' : 'Choose report dates and balance sheet date.' }}</p>
            </div>
        </div>
        <form class="filter-grid" method="GET" action="{{ route('admin.finance.reports', app()->getLocale()) }}">
            <div><label class="form-label">{{ $isAr ? 'من تاريخ' : 'Date from' }}</label><input class="form-control" type="date" name="date_from" value="{{ $dateFrom }}"></div>
            <div><label class="form-label">{{ $isAr ? 'إلى تاريخ' : 'Date to' }}</label><input class="form-control" type="date" name="date_to" value="{{ $dateTo }}"></div>
            <div><label class="form-label">{{ $isAr ? 'تاريخ الميزانية' : 'Balance date' }}</label><input class="form-control" type="date" name="balance_date" value="{{ $balanceSheetDate }}"></div>
            <div class="d-flex align-items-end gap-2 flex-wrap"><button class="btn btn-primary">{{ $isAr ? 'تحديث التقارير' : 'Refresh reports' }}</button><a class="btn btn-outline-secondary" href="{{ route('admin.finance.reports', app()->getLocale()) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a></div>
        </form>
    </section>

    <section class="stats-grid">
        <div class="stat-card"><small>{{ $isAr ? 'الإيرادات' : 'Revenues' }}</small><strong>{{ number_format((float) $incomeStatement['revenues'], 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'المصروفات' : 'Expenses' }}</small><strong>{{ number_format((float) $incomeStatement['expenses'], 2) }}</strong></div>
        <div class="stat-card"><small>{{ $isAr ? 'صافي الربح' : 'Net profit' }}</small><strong>{{ number_format((float) $incomeStatement['net_profit'], 2) }}</strong></div>
    </section>

    <section class="workspace-grid">
        <div class="stack">
            <div class="panel-card">
                <div class="panel-head mb-3"><div><h2 class="panel-title">{{ $isAr ? 'ملخص قائمة الدخل' : 'Income statement summary' }}</h2><p class="panel-subtitle">{{ $isAr ? 'ملخص مباشر لأداء الفترة المحددة.' : 'Direct summary for the selected period.' }}</p></div></div>
                <div class="summary-list">
                    <div class="summary-row"><span>{{ $isAr ? 'الإيرادات' : 'Revenues' }}</span><strong class="text-success">{{ number_format((float) $incomeStatement['revenues'], 2) }}</strong></div>
                    <div class="summary-row"><span>{{ $isAr ? 'المصروفات' : 'Expenses' }}</span><strong class="text-danger">{{ number_format((float) $incomeStatement['expenses'], 2) }}</strong></div>
                    <div class="summary-row"><span>{{ $isAr ? 'صافي الربح' : 'Net profit' }}</span><strong>{{ number_format((float) $incomeStatement['net_profit'], 2) }}</strong></div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3"><div><h2 class="panel-title">{{ $isAr ? 'الميزانية العمومية' : 'Balance sheet' }}</h2><p class="panel-subtitle">{{ $isAr ? 'تفصيل الأصول والخصوم وحقوق الملكية حتى التاريخ المحدد.' : 'Assets, liabilities, and equity up to the selected date.' }}</p></div></div>
                <div class="balance-grid">
                    <div class="balance-card">
                        <h6>{{ $isAr ? 'الأصول' : 'Assets' }}</h6>
                        @forelse($balanceSheet['assets'] as $row)
                            <div class="balance-row"><span>{{ $row['code'] }} - {{ $row['name'] }}</span><strong>{{ number_format((float) $row['balance'], 2) }}</strong></div>
                        @empty
                            <div class="text-muted">{{ $isAr ? 'لا توجد أرصدة' : 'No balances' }}</div>
                        @endforelse
                    </div>
                    <div class="balance-card">
                        <h6>{{ $isAr ? 'الخصوم' : 'Liabilities' }}</h6>
                        @forelse($balanceSheet['liabilities'] as $row)
                            <div class="balance-row"><span>{{ $row['code'] }} - {{ $row['name'] }}</span><strong>{{ number_format((float) $row['balance'], 2) }}</strong></div>
                        @empty
                            <div class="text-muted">{{ $isAr ? 'لا توجد أرصدة' : 'No balances' }}</div>
                        @endforelse
                    </div>
                    <div class="balance-card">
                        <h6>{{ $isAr ? 'حقوق الملكية' : 'Equity' }}</h6>
                        @forelse($balanceSheet['equity'] as $row)
                            <div class="balance-row"><span>{{ $row['code'] }} - {{ $row['name'] }}</span><strong>{{ number_format((float) $row['balance'], 2) }}</strong></div>
                        @empty
                            <div class="text-muted">{{ $isAr ? 'لا توجد أرصدة' : 'No balances' }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3"><div><h2 class="panel-title">{{ $isAr ? 'أعمار الذمم المدينة والدائنة' : 'Receivables & payables aging' }}</h2><p class="panel-subtitle">{{ $isAr ? 'جداول أوضح لتأخر الاستحقاقات حسب الفئات الزمنية.' : 'Clearer aging tables by due buckets.' }}</p></div></div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="table-shell">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th colspan="5">{{ $isAr ? 'ذمم مدينة' : 'Receivables' }}</th></tr><tr><th>{{ $isAr ? 'الفاتورة' : 'Invoice' }}</th><th>{{ $isAr ? 'الطرف' : 'Party' }}</th><th>{{ $isAr ? 'الاستحقاق' : 'Due' }}</th><th>{{ $isAr ? 'الرصيد' : 'Balance' }}</th><th>{{ $isAr ? 'الفئة' : 'Bucket' }}</th></tr></thead>
                                    <tbody>
                                    @forelse($aging['receivables'] as $row)
                                        <tr><td>{{ $row['invoice_no'] }}</td><td>{{ $row['party'] }}</td><td>{{ $row['due_date'] }}</td><td>{{ number_format((float) $row['balance_due'], 2) }}</td><td>{{ $row['bucket'] }}</td></tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">{{ $isAr ? 'لا توجد ذمم مدينة مفتوحة.' : 'No open receivables.' }}</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="table-shell">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead><tr><th colspan="5">{{ $isAr ? 'ذمم دائنة' : 'Payables' }}</th></tr><tr><th>{{ $isAr ? 'الفاتورة' : 'Invoice' }}</th><th>{{ $isAr ? 'الطرف' : 'Party' }}</th><th>{{ $isAr ? 'الاستحقاق' : 'Due' }}</th><th>{{ $isAr ? 'الرصيد' : 'Balance' }}</th><th>{{ $isAr ? 'الفئة' : 'Bucket' }}</th></tr></thead>
                                    <tbody>
                                    @forelse($aging['payables'] as $row)
                                        <tr><td>{{ $row['invoice_no'] }}</td><td>{{ $row['party'] }}</td><td>{{ $row['due_date'] }}</td><td>{{ number_format((float) $row['balance_due'], 2) }}</td><td>{{ $row['bucket'] }}</td></tr>
                                    @empty
                                        <tr><td colspan="5" class="text-center py-4 text-muted">{{ $isAr ? 'لا توجد ذمم دائنة مفتوحة.' : 'No open payables.' }}</td></tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="stack">
            <div class="panel-card">
                <div class="panel-head mb-3"><div><h2 class="panel-title">{{ $isAr ? 'الإقفال الشهري' : 'Monthly closing' }}</h2><p class="panel-subtitle">{{ $isAr ? 'حفظ إقفال فترة شهرية مع ملاحظات داخلية.' : 'Close a monthly period with optional notes.' }}</p></div></div>
                <form class="row g-3" method="POST" action="{{ route('admin.finance.reports.closing.store', app()->getLocale()) }}">
                    @csrf
                    <div class="col-6"><label class="form-label">{{ $isAr ? 'السنة' : 'Year' }}</label><input class="form-control" type="number" name="year" value="{{ now()->year }}" min="2020" max="2100"></div>
                    <div class="col-6"><label class="form-label">{{ $isAr ? 'الشهر' : 'Month' }}</label><input class="form-control" type="number" name="month" value="{{ now()->month }}" min="1" max="12"></div>
                    <div class="col-12"><label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label><textarea class="form-control" name="notes" rows="4" placeholder="{{ $isAr ? 'ملاحظات الإقفال' : 'Closing notes' }}"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">{{ $isAr ? 'إقفال الشهر' : 'Close month' }}</button></div>
                </form>
            </div>

            <div class="panel-card">
                <div class="panel-head mb-3"><div><h2 class="panel-title">{{ $isAr ? 'سجل الإقفالات' : 'Closing history' }}</h2><p class="panel-subtitle">{{ $isAr ? 'آخر الفترات المقفلة مع صافي النتائج.' : 'Recent closed periods and net results.' }}</p></div></div>
                <div class="table-shell">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>{{ $isAr ? 'الفترة' : 'Period' }}</th><th>{{ $isAr ? 'الإيراد' : 'Income' }}</th><th>{{ $isAr ? 'المصروف' : 'Expense' }}</th><th>{{ $isAr ? 'الصافي' : 'Net' }}</th><th>{{ $isAr ? 'التاريخ' : 'Closed at' }}</th></tr></thead>
                            <tbody>
                            @forelse($closings as $closing)
                                <tr><td>{{ $closing->period_key }}</td><td>{{ number_format((float) $closing->income_total, 2) }}</td><td>{{ number_format((float) $closing->expense_total, 2) }}</td><td>{{ number_format((float) $closing->net_profit, 2) }}</td><td>{{ optional($closing->closed_at)->format('Y-m-d H:i') }}</td></tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">{{ $isAr ? 'لا توجد إقفالات محفوظة حتى الآن.' : 'No closings saved yet.' }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3">{{ $closings->links() }}</div>
            </div>
        </div>
    </section>
</div>
@endsection
