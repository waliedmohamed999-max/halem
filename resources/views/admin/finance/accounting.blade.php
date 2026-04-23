@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'المحاسبة العامة' : 'General Accounting' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'دليل الحسابات وميزان المراجعة والقيود اليومية المزدوجة' : 'Chart of accounts, trial balance, and double-entry journals' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-outline-secondary" href="{{ route('admin.finance.index', app()->getLocale()) }}">{{ $isAr ? 'العودة إلى المالية' : 'Back to Finance' }}</a>
            <form method="POST" action="{{ route('admin.finance.accounting.sync', app()->getLocale()) }}">
                @csrf
                <button class="btn btn-primary">{{ $isAr ? 'مزامنة القيود' : 'Sync Journals' }}</button>
            </form>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-panel"><small>{{ $isAr ? 'الحسابات' : 'Accounts' }}</small><strong>{{ $summary['accounts'] }}</strong><span>{{ $isAr ? 'الحسابات النشطة' : 'Active chart accounts' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'القيود المرحلة' : 'Posted Journals' }}</small><strong>{{ $summary['journals'] }}</strong><span>{{ $isAr ? 'القيود اليومية المحاسبية' : 'Posted accounting journals' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'إجمالي المدين' : 'Total Debits' }}</small><strong>{{ number_format($summary['debits'], 2) }}</strong><span>{{ $isAr ? 'في النطاق الحالي' : 'Within current range' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'إجمالي الدائن' : 'Total Credits' }}</small><strong>{{ number_format($summary['credits'], 2) }}</strong><span>{{ $isAr ? 'في النطاق الحالي' : 'Within current range' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'الإيرادات' : 'Revenues' }}</small><strong>{{ number_format($summary['revenues'], 2) }}</strong><span>{{ $isAr ? 'الحسابات الإيرادية' : 'Revenue accounts' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'المصروفات' : 'Expenses' }}</small><strong>{{ number_format($summary['expenses'], 2) }}</strong><span>{{ $isAr ? 'الحسابات المصروفية' : 'Expense accounts' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'صافي الربح' : 'Net Profit' }}</small><strong>{{ number_format($summary['net_profit'], 2) }}</strong><span>{{ $isAr ? 'إيرادات ناقص مصروفات' : 'Revenue minus expenses' }}</span></div>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="admin-list-card">
                <div class="patient-panel-head">
                    <h5 class="mb-0">{{ $isAr ? 'مقارنة المدين والدائن' : 'Debit vs Credit' }}</h5>
                </div>
                <div style="height: 320px;">
                    <canvas id="accountingFlowChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="admin-list-card">
                <div class="patient-panel-head">
                    <h5 class="mb-0">{{ $isAr ? 'هيكل النتائج' : 'Result Mix' }}</h5>
                </div>
                <div style="height: 320px;">
                    <canvas id="accountingResultChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-filter-card">
        <form class="row g-2" method="GET" action="{{ route('admin.finance.accounting', app()->getLocale()) }}">
            <div class="col-md-3">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-4">
                <select class="form-select" name="account_id">
                    <option value="">{{ $isAr ? 'كل الحسابات' : 'All accounts' }}</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected((string) request('account_id') === (string) $account->id)>{{ $account->code }} - {{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-primary w-100">{{ $isAr ? 'تطبيق' : 'Apply' }}</button>
                <a class="btn btn-outline-secondary w-100" href="{{ route('admin.finance.accounting', app()->getLocale()) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a>
            </div>
        </form>
    </div>

    <div class="admin-list-card">
        <div class="patient-panel-head">
            <h5 class="mb-0">{{ $isAr ? 'ميزان المراجعة' : 'Trial Balance' }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'الكود' : 'Code' }}</th>
                    <th>{{ $isAr ? 'الحساب' : 'Account' }}</th>
                    <th>{{ $isAr ? 'الفئة' : 'Category' }}</th>
                    <th>{{ $isAr ? 'مدين' : 'Debit' }}</th>
                    <th>{{ $isAr ? 'دائن' : 'Credit' }}</th>
                    <th>{{ $isAr ? 'الرصيد' : 'Balance' }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($trialBalance as $account)
                    <tr>
                        <td>{{ $account->code }}</td>
                        <td class="fw-semibold">{{ $account->name }}</td>
                        <td><span class="admin-status-pill is-muted">{{ $account->category }}</span></td>
                        <td>{{ number_format((float) ($account->debit_sum ?? 0), 2) }}</td>
                        <td>{{ number_format((float) ($account->credit_sum ?? 0), 2) }}</td>
                        <td class="{{ $account->balance >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format((float) $account->balance, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-list-card">
        <div class="patient-panel-head">
            <h5 class="mb-0">{{ $isAr ? 'دفتر القيود اليومية' : 'Journal Ledger' }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>{{ $isAr ? 'رقم القيد' : 'Journal No.' }}</th>
                    <th>{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                    <th>{{ $isAr ? 'الوصف' : 'Description' }}</th>
                    <th>{{ $isAr ? 'الحسابات' : 'Accounts' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($journals as $journal)
                    <tr>
                        <td>{{ $journal->journal_no }}</td>
                        <td>{{ optional($journal->journal_date)->format('Y-m-d') }}</td>
                        <td>
                            <div class="fw-semibold">{{ $journal->description }}</div>
                            <small class="text-muted">{{ $journal->financeEntry?->title ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @foreach($journal->lines as $line)
                                    <div>
                                        <span class="fw-semibold">{{ $line->account?->code }} - {{ $line->account?->name }}</span>
                                        <small class="text-muted">
                                            {{ $isAr ? 'مدين' : 'Dr' }} {{ number_format((float) $line->debit, 2) }}
                                            /
                                            {{ $isAr ? 'دائن' : 'Cr' }} {{ number_format((float) $line->credit, 2) }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td><span class="admin-status-pill {{ $journal->status === 'posted' ? '' : 'is-muted' }}">{{ $journal->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty">{{ $isAr ? 'لا توجد قيود محاسبية' : 'No accounting journals found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $journals->links() }}</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const flowCanvas = document.getElementById('accountingFlowChart');
    const resultCanvas = document.getElementById('accountingResultChart');
    const palette = {
        primary: '#1d8f78',
        primarySoft: 'rgba(29, 143, 120, 0.18)',
        secondary: '#79c7b4',
        dark: '#123b35',
        soft: '#dff4ee'
    };

    if (flowCanvas && window.Chart) {
        new Chart(flowCanvas, {
            type: 'bar',
            data: {
                labels: {!! \Illuminate\Support\Js::from([
                    $isAr ? 'المدين' : 'Debits',
                    $isAr ? 'الدائن' : 'Credits',
                ]) !!},
                datasets: [{
                    data: {!! \Illuminate\Support\Js::from([
                        (float) $summary['debits'],
                        (float) $summary['credits'],
                    ]) !!},
                    backgroundColor: [palette.primary, palette.secondary],
                    borderRadius: 14,
                    borderSkipped: false,
                    maxBarThickness: 72
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: palette.dark }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: palette.primarySoft },
                        ticks: { color: palette.dark }
                    }
                }
            }
        });
    }

    if (resultCanvas && window.Chart) {
        new Chart(resultCanvas, {
            type: 'doughnut',
            data: {
                labels: {!! \Illuminate\Support\Js::from([
                    $isAr ? 'الإيرادات' : 'Revenues',
                    $isAr ? 'المصروفات' : 'Expenses',
                    $isAr ? 'صافي الربح' : 'Net Profit',
                ]) !!},
                datasets: [{
                    data: {!! \Illuminate\Support\Js::from([
                        max((float) $summary['revenues'], 0),
                        max((float) $summary['expenses'], 0),
                        max((float) $summary['net_profit'], 0),
                    ]) !!},
                    backgroundColor: [palette.primary, palette.secondary, palette.soft],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: palette.dark,
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }
});
</script>
@endsection
