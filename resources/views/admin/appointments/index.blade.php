@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="admin-list-page">
    <div class="admin-list-header">
        <div>
            <h4 class="admin-list-title">{{ $isAr ? 'إدارة الحجوزات' : 'Appointments Management' }}</h4>
            <p class="admin-list-subtitle">{{ $isAr ? 'مراقبة الحجوزات، الفلاتر، والتحويل إلى زيارات من شاشة واحدة' : 'Monitor bookings, filter results, and convert appointments to visits from one workspace' }}</p>
        </div>
        <div class="admin-list-actions">
            <a class="btn btn-primary" href="{{ route('admin.appointments.create', app()->getLocale()) }}">{{ $isAr ? 'إضافة حجز' : 'Add Appointment' }}</a>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-panel"><small>{{ $isAr ? 'حجوزات اليوم' : 'Today' }}</small><strong>{{ $stats['today'] }}</strong><span>{{ $isAr ? 'الموعد الحالي لليوم' : 'Scheduled for today' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'حجوزات الأسبوع' : 'This Week' }}</small><strong>{{ $stats['week'] }}</strong><span>{{ $isAr ? 'نطاق الأسبوع الحالي' : 'Current weekly scope' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'جديدة' : 'New' }}</small><strong>{{ $stats['new'] }}</strong><span>{{ $isAr ? 'بانتظار المتابعة' : 'Awaiting follow-up' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'تم تحويلها لزيارة' : 'Converted' }}</small><strong>{{ $stats['converted'] }}</strong><span>{{ $isAr ? 'أصبحت جزءًا من الملف الطبي' : 'Now linked to patient records' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'الإجمالي' : 'Total' }}</small><strong>{{ $stats['total'] }}</strong><span>{{ $isAr ? 'كل الحجوزات في النظام' : 'All appointments in the system' }}</span></div>
        <div class="admin-stat-panel"><small>{{ $isAr ? 'إجمالي قيمة الحجوزات' : 'Booking Value' }}</small><strong>{{ number_format((float) ($stats['revenue'] ?? 0), 2) }}</strong><span>{{ $isAr ? 'قيمة الحجوزات الحالية' : 'Current appointments value' }}</span></div>
    </div>

    <div class="admin-filter-card">
        <form class="row g-2" method="GET" action="{{ route('admin.appointments.index', app()->getLocale()) }}">
            <div class="col-md-4">
                <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="{{ $isAr ? 'بحث بالاسم أو الهاتف أو الملاحظات' : 'Search by patient, phone, or notes' }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">{{ $isAr ? 'كل الحالات' : 'All statuses' }}</option>
                    <option value="new" @selected(request('status') === 'new')>{{ $isAr ? 'جديد' : 'New' }}</option>
                    <option value="contacted" @selected(request('status') === 'contacted')>{{ $isAr ? 'تم التواصل' : 'Contacted' }}</option>
                    <option value="completed" @selected(request('status') === 'completed')>{{ $isAr ? 'مكتمل' : 'Completed' }}</option>
                    <option value="canceled" @selected(request('status') === 'canceled')>{{ $isAr ? 'ملغي' : 'Canceled' }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="branch_id">
                    <option value="">{{ $isAr ? 'كل الفروع' : 'All branches' }}</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="source">
                    <option value="">{{ $isAr ? 'كل المصادر' : 'All sources' }}</option>
                    <option value="website" @selected(request('source') === 'website')>{{ $isAr ? 'الموقع' : 'Website' }}</option>
                    <option value="whatsapp" @selected(request('source') === 'whatsapp')>{{ $isAr ? 'واتساب' : 'WhatsApp' }}</option>
                    <option value="phone" @selected(request('source') === 'phone')>{{ $isAr ? 'هاتف' : 'Phone' }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="booking_type">
                    <option value="">{{ $isAr ? 'كل الأنواع' : 'All types' }}</option>
                    <option value="regular" @selected(request('booking_type') === 'regular')>{{ $isAr ? 'عادي' : 'Regular' }}</option>
                    <option value="vip" @selected(request('booking_type') === 'vip')>VIP</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="converted">
                    <option value="">{{ $isAr ? 'التحويل' : 'Conversion' }}</option>
                    <option value="yes" @selected(request('converted') === 'yes')>{{ $isAr ? 'تم التحويل' : 'Converted' }}</option>
                    <option value="no" @selected(request('converted') === 'no')>{{ $isAr ? 'غير محول' : 'Not converted' }}</option>
                </select>
            </div>
            <div class="col-md-2"><input class="form-control" type="date" name="date_from" value="{{ request('date_from') }}"></div>
            <div class="col-md-2"><input class="form-control" type="date" name="date_to" value="{{ request('date_to') }}"></div>
            <div class="col-md-2">
                <select class="form-select" name="sort">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>{{ $isAr ? 'الأحدث إضافة' : 'Latest created' }}</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>{{ $isAr ? 'الأقدم إضافة' : 'Oldest created' }}</option>
                    <option value="date_asc" @selected(request('sort') === 'date_asc')>{{ $isAr ? 'الأقرب موعدًا' : 'Nearest date' }}</option>
                    <option value="date_desc" @selected(request('sort') === 'date_desc')>{{ $isAr ? 'الأبعد موعدًا' : 'Farthest date' }}</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2 flex-wrap">
                <button class="btn btn-primary">{{ $isAr ? 'تطبيق الفلاتر' : 'Apply Filters' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.appointments.index', app()->getLocale()) }}">{{ $isAr ? 'إعادة ضبط' : 'Reset' }}</a>
            </div>
        </form>
    </div>

    <div class="admin-list-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $isAr ? 'المريض' : 'Patient' }}</th>
                    <th>{{ $isAr ? 'الفرع / الخدمة' : 'Branch / Service' }}</th>
                    <th>{{ $isAr ? 'التاريخ / الوقت' : 'Date / Time' }}</th>
                    <th>{{ $isAr ? 'النوع / السعر' : 'Type / Price' }}</th>
                    <th>{{ $isAr ? 'المصدر' : 'Source' }}</th>
                    <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                    <th>{{ $isAr ? 'التحويل' : 'Conversion' }}</th>
                    <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($appointments as $appointment)
                    <tr>
                        <td>{{ $appointment->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $appointment->patient_name }}</div>
                            <small class="text-muted">{{ $appointment->patient_phone }}</small>
                        </td>
                        <td>
                            <div>{{ $appointment->branch?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $appointment->service?->title ?? '-' }}</small>
                        </td>
                        <td>
                            <div>{{ $appointment->preferred_date?->format('Y-m-d') ?? '-' }}</div>
                            <small class="text-muted">{{ $appointment->preferred_time ?: '-' }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $appointment->booking_type === 'vip' ? 'VIP' : ($isAr ? 'عادي' : 'Regular') }}</div>
                            <small class="text-muted">{{ number_format((float) $appointment->price, 2) }}</small>
                        </td>
                        <td><span class="admin-status-pill is-muted">{{ $appointment->source ?? 'website' }}</span></td>
                        <td style="min-width: 180px;">
                            <form method="POST" action="{{ route('admin.appointments.quick-status', [app()->getLocale(), $appointment->id]) }}">
                                @csrf
                                <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                                    <option value="new" @selected($appointment->status === 'new')>{{ $isAr ? 'جديد' : 'New' }}</option>
                                    <option value="contacted" @selected($appointment->status === 'contacted')>{{ $isAr ? 'تم التواصل' : 'Contacted' }}</option>
                                    <option value="completed" @selected($appointment->status === 'completed')>{{ $isAr ? 'مكتمل' : 'Completed' }}</option>
                                    <option value="canceled" @selected($appointment->status === 'canceled')>{{ $isAr ? 'ملغي' : 'Canceled' }}</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-nowrap">
                            @if($appointment->visit)
                                <a class="btn btn-sm btn-outline-success" href="{{ route('admin.patients.show', [app()->getLocale(), $appointment->visit->patient_id]) }}">{{ $isAr ? 'تم التحويل' : 'Converted' }}</a>
                            @else
                                <form method="POST" action="{{ route('admin.appointments.convert-to-visit', [app()->getLocale(), $appointment->id]) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-success">{{ $isAr ? 'تحويل لزيارة' : 'Convert' }}</button>
                                </form>
                            @endif
                        </td>
                        <td>
                            <div class="admin-table-actions">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.appointments.show', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'عرض' : 'View' }}</a>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('admin.appointments.report', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'تقرير' : 'Report' }}</a>
                                <a class="btn btn-sm btn-outline-dark" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $appointment->patient_phone) }}" target="_blank">{{ $isAr ? 'واتساب' : 'WhatsApp' }}</a>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.appointments.edit', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form method="POST" action="{{ route('admin.appointments.destroy', [app()->getLocale(), $appointment->id]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف هذا الحجز؟' : 'Delete this appointment?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="admin-empty">{{ $isAr ? 'لا توجد حجوزات مطابقة' : 'No matching appointments found' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $appointments->links() }}</div>
</div>
@endsection
