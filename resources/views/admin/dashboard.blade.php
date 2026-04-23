@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .kpi-card { border: 1px solid #d8e5fb; border-radius: 1.1rem; background: linear-gradient(180deg, #ffffff 0%, #f7faff 100%); padding: 1rem; height: 100%; box-shadow: 0 14px 30px rgba(41, 87, 146, 0.08); }
    .kpi-label { color: #68809d; font-size: .84rem; margin-bottom: .35rem; display: block; font-weight: 700; }
    .kpi-value { font-size: 1.9rem; font-weight: 900; color: #153b6d; line-height: 1; }
    .dash-box { border: 1px solid #d8e5fb; border-radius: 1.1rem; background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%); padding: 1rem; height: 100%; box-shadow: 0 14px 30px rgba(41, 87, 146, 0.08); }
    .small-muted { color: #68809d; font-size: .86rem; }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h3 class="mb-0">{{ $isAr ? 'لوحة المؤشرات الذكية' : 'Smart Dashboard' }}</h3>
    <div class="d-flex gap-2 flex-wrap">
        <form method="GET" action="{{ route('admin.dashboard', app()->getLocale()) }}" class="d-flex gap-2">
            <select name="days" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="7" @selected(($days ?? 14) === 7)>{{ $isAr ? 'آخر 7 أيام' : 'Last 7 days' }}</option>
                <option value="14" @selected(($days ?? 14) === 14)>{{ $isAr ? 'آخر 14 يوم' : 'Last 14 days' }}</option>
                <option value="30" @selected(($days ?? 14) === 30)>{{ $isAr ? 'آخر 30 يوم' : 'Last 30 days' }}</option>
            </select>
        </form>
        <a class="btn btn-sm btn-success" href="{{ route('admin.dashboard.daily-report-pdf', app()->getLocale()) }}">
            {{ $isAr ? 'تصدير تقرير اليوم PDF' : 'Export Daily PDF' }}
        </a>
    </div>
</div>

@if(!empty($alerts))
    <div class="mb-3">
        @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['level'] }} d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong>{{ $isAr ? $alert['title_ar'] : $alert['title'] }}</strong>
                    <div class="small">{{ $isAr ? $alert['message_ar'] : $alert['message'] }}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge text-bg-light">{{ $alert['count'] }}</span>
                    <a class="btn btn-sm btn-outline-dark" href="{{ $alert['url'] }}">{{ $isAr ? 'متابعة' : 'Open' }}</a>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'حجوزات اليوم' : 'Today Appointments' }}</span><div class="kpi-value">{{ $stats['appointments_today'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'إجمالي الحجوزات (الفترة)' : 'Total Appointments (Period)' }}</span><div class="kpi-value">{{ $stats['appointments_total'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'نسبة الإنجاز' : 'Completion Rate' }}</span><div class="kpi-value">{{ $stats['completion_rate'] }}%</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'نسبة الإلغاء' : 'Cancellation Rate' }}</span><div class="kpi-value">{{ $stats['cancellation_rate'] }}%</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'الحجوزات الجديدة' : 'New Appointments' }}</span><div class="kpi-value">{{ $stats['appointments_new'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'الحجوزات المكتملة' : 'Completed' }}</span><div class="kpi-value">{{ $stats['appointments_completed'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'الحجوزات الملغاة' : 'Canceled' }}</span><div class="kpi-value">{{ $stats['appointments_canceled'] }}</div></div></div>
    <div class="col-6 col-lg-3"><div class="kpi-card"><span class="kpi-label">{{ $isAr ? 'قيمة الحجوزات (الفترة)' : 'Booking Value (Period)' }}</span><div class="kpi-value">{{ number_format((float) ($stats['revenue_period'] ?? 0), 2) }}</div></div></div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6"><div class="dash-box"><h6>{{ $isAr ? 'الحجوزات حسب الفروع' : 'Appointments by Branch' }}</h6><canvas id="branchChart" height="120"></canvas></div></div>
    <div class="col-lg-3"><div class="dash-box"><h6>{{ $isAr ? 'توزيع الحالات' : 'Status Distribution' }}</h6><canvas id="statusChart" height="120"></canvas></div></div>
    <div class="col-lg-3"><div class="dash-box"><h6>{{ $isAr ? 'النشاط اليومي' : 'Daily Activity' }}</h6><canvas id="daysChart" height="120"></canvas></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="dash-box">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">{{ $isAr ? 'مواعيد اليوم' : 'Today Schedule' }}</h6>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.appointments.index', app()->getLocale()) }}">{{ $isAr ? 'كل الحجوزات' : 'All Appointments' }}</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>{{ $isAr ? 'الوقت' : 'Time' }}</th>
                        <th>{{ $isAr ? 'المريض' : 'Patient' }}</th>
                        <th>{{ $isAr ? 'الفرع/الخدمة' : 'Branch/Service' }}</th>
                        <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($todayAppointments as $item)
                        <tr>
                            <td>{{ $item->preferred_time ?: '-' }}</td>
                            <td>{{ $item->patient_name }}<br><span class="small-muted">{{ $item->patient_phone }}</span></td>
                            <td>{{ $item->branch?->name ?? '-' }}<br><span class="small-muted">{{ $item->service?->title ?? '-' }}</span></td>
                            <td><span class="badge text-bg-light">{{ $item->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">{{ $isAr ? 'لا توجد مواعيد اليوم' : 'No appointments for today' }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="dash-box h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">{{ $isAr ? 'آخر الرسائل' : 'Latest Messages' }}</h6>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.messages.index', app()->getLocale()) }}">{{ $isAr ? 'عرض الكل' : 'View all' }}</a>
            </div>
            <div class="list-group list-group-flush">
                @forelse($latestMessages as $message)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between gap-2">
                            <strong>{{ $message->visitor_name }}</strong>
                            <span class="badge {{ $message->admin_unread_count > 0 ? 'text-bg-danger' : 'text-bg-success' }}">{{ $message->status }}</span>
                        </div>
                        <div class="small-muted">{{ $message->visitor_phone }} {{ $message->visitor_email ? '| ' . $message->visitor_email : '' }}</div>
                        <div class="small text-truncate">{{ $message->last_message_preview }}</div>
                    </div>
                @empty
                    <div class="text-muted py-3">{{ $isAr ? 'لا توجد رسائل حاليًا' : 'No messages yet' }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
fetch("{{ route('admin.charts', ['locale' => app()->getLocale(), 'days' => $days]) }}")
    .then(function (r) { return r.json(); })
    .then(function (data) {
        var byBranch = data.by_branch || [];
        var byStatus = data.by_status || [];
        var byDays = data.last_14_days || [];

        new Chart(document.getElementById('branchChart'), {
            type: 'bar',
            data: {
                labels: byBranch.map(function (x) { return x.branch || 'N/A'; }),
                datasets: [{ label: "{{ $isAr ? 'الحجوزات' : 'Appointments' }}", data: byBranch.map(function (x) { return x.total || 0; }), backgroundColor: '#2f6fed', borderRadius: 10, maxBarThickness: 36 }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });

        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: byStatus.map(function (x) { return x.status || '-'; }),
                datasets: [{ data: byStatus.map(function (x) { return x.total || 0; }), backgroundColor: ['#2f6fed', '#6fa8ff', '#15b79e', '#f97316'], borderWidth: 0 }]
            }
        });

        new Chart(document.getElementById('daysChart'), {
            type: 'line',
            data: {
                labels: byDays.map(function (x) { return x.day; }),
                datasets: [{ label: "{{ $isAr ? 'الحجوزات' : 'Appointments' }}", data: byDays.map(function (x) { return x.total || 0; }), borderColor: '#2f6fed', backgroundColor: 'rgba(47,111,237,.12)', pointBackgroundColor: '#2f6fed', pointBorderColor: '#ffffff', pointBorderWidth: 2, fill: true, tension: .35 }]
            },
            options: { scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
        });
    });
</script>
@endsection
