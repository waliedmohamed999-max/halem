@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'ملفات المرضى' : 'Patient Files' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'مرجع المريض الشامل مع الزيارات والحجوزات والملفات الطبية' : 'Comprehensive patient references with visits, appointments, and medical files' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.patients.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة مريض' : 'Add Patient' }}</a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-panel"><small>{{ $isAr ? 'إجمالي الملفات' : 'Total Profiles' }}</small><strong>{{ $stats['total'] }}</strong><span>{{ $isAr ? 'عدد ملفات المرضى الحالية' : 'Current patient records' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'لديهم زيارات' : 'With Visits' }}</small><strong>{{ $stats['with_visits'] }}</strong><span>{{ $isAr ? 'ملفات مرتبطة بتاريخ علاجي' : 'Profiles with treatment history' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'لديهم حجوزات' : 'With Appointments' }}</small><strong>{{ $stats['with_appointments'] }}</strong><span>{{ $isAr ? 'مرتبطة بسجل حجوزات' : 'Linked to appointment history' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'بها ملفات طبية' : 'With Documents' }}</small><strong>{{ $stats['with_documents'] }}</strong><span>{{ $isAr ? 'أشعة أو تحاليل أو تقارير' : 'Radiology, labs, or reports attached' }}</span></div>
    </div>

    <div class="admin-filter-card">
        <form class="row g-2" method="GET" action="{{ route('admin.patients.index', app()->getLocale()) }}">
            <div class="col-md-8">
                <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="{{ $isAr ? 'ابحث بالاسم أو الهاتف أو الرقم القومي' : 'Search by name, phone, or national ID' }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary w-100">{{ $isAr ? 'بحث' : 'Search' }}</button>
                <a class="btn btn-outline-secondary w-100" href="{{ route('admin.patients.index', app()->getLocale()) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a>
            </div>
        </form>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'الاسم' : 'Name' }}</th>
                    <th>{{ $isAr ? 'الهاتف' : 'Phone' }}</th>
                    <th>{{ $isAr ? 'آخر زيارة' : 'Last Visit' }}</th>
                    <th>{{ $isAr ? 'الزيارات' : 'Visits' }}</th>
                    <th>{{ $isAr ? 'الحجوزات' : 'Appointments' }}</th>
                    <th>{{ $isAr ? 'الملفات الطبية' : 'Documents' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>{{ $patient->id }}</td>
                        <td class="fw-semibold">{{ $patient->full_name }}</td>
                        <td>{{ $patient->phone }}</td>
                        <td>{{ $patient->last_visit_at ? $patient->last_visit_at->format('Y-m-d') : '-' }}</td>
                        <td><span class="admin-status-pill is-muted">{{ $patient->visits_count }}</span></td>
                        <td><span class="admin-status-pill is-muted">{{ $patient->appointments_count }}</span></td>
                        <td><span class="admin-status-pill is-muted">{{ $patient->documents_count }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.patients.show', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'الملف الشامل' : 'Full Profile' }}</a>
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.patients.edit', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="POST" action="{{ route('admin.patients.destroy', [app()->getLocale(), $patient->id]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف ملف المريض؟' : 'Delete patient file?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="admin-empty">{{ $isAr ? 'لا توجد بيانات مرضى' : 'No patient files found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $patients->links() }}</div>
</div>
@endsection
