@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<style>
    .patient-profile { display: flex; flex-direction: column; gap: 1rem; }
    .patient-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(320px, 1fr);
        gap: 1rem;
        padding: 1.2rem;
        border: 1px solid #dbe8fb;
        border-radius: 1.6rem;
        background: linear-gradient(135deg, rgba(24, 65, 118, .96), rgba(39, 90, 168, .92));
        color: #fff;
    }
    .patient-badge {
        display: inline-flex;
        align-items: center;
        padding: .35rem .7rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        color: #e7f0ff;
        font-size: .76rem;
        font-weight: 800;
    }
    .patient-name { margin: .7rem 0 .35rem; font-size: 1.9rem; font-weight: 800; }
    .patient-meta { display: flex; flex-wrap: wrap; gap: .55rem; margin-top: .8rem; }
    .patient-meta span {
        display: inline-flex;
        align-items: center;
        padding: .5rem .82rem;
        border-radius: 999px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.08);
        font-size: .82rem;
    }
    .patient-actions { display: flex; flex-wrap: wrap; gap: .6rem; justify-content: flex-end; }
    .patient-actions .btn { border-radius: 1rem; font-weight: 800; }
    .patient-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .9rem;
    }
    .patient-stat-card {
        padding: 1rem;
        border: 1px solid #dbe8fb;
        border-radius: 1.35rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .patient-stat-card small { display: block; color: #6d829a; font-weight: 700; margin-bottom: .45rem; }
    .patient-stat-card strong { font-size: 1.5rem; line-height: 1; }
    .patient-grid-two {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
        gap: 1rem;
    }
    .patient-panel {
        border: 1px solid #dbe8fb;
        border-radius: 1.5rem;
        background: rgba(255,255,255,.86);
        overflow: hidden;
    }
    .patient-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        border-bottom: 1px solid #e4eefb;
        background: linear-gradient(180deg, #f8fbff 0%, #f0f5ff 100%);
    }
    .patient-panel-head h5, .patient-panel-head h6 { margin: 0; font-weight: 800; }
    .patient-panel-body { padding: 1rem 1.1rem; }
    .patient-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }
    .patient-info-item {
        padding: .85rem .9rem;
        border: 1px solid #e0ebfa;
        border-radius: 1rem;
        background: #f9fbff;
    }
    .patient-info-item span { display: block; color: #7288a1; font-size: .77rem; font-weight: 700; margin-bottom: .28rem; }
    .patient-alerts { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
    .patient-alert {
        padding: .95rem 1rem;
        border: 1px solid #ffd4c8;
        border-radius: 1.1rem;
        background: linear-gradient(180deg, #fff8f5 0%, #fff3ee 100%);
    }
    .patient-alert strong { display: block; margin-bottom: .35rem; color: #9d3d25; }
    .patient-list {
        display: flex;
        flex-direction: column;
        gap: .7rem;
    }
    .patient-list-card {
        padding: .95rem 1rem;
        border: 1px solid #dce8f9;
        border-radius: 1.15rem;
        background: #fbfdff;
    }
    .patient-list-card strong { display: block; margin-bottom: .28rem; }
    .patient-list-card small { color: #7288a1; }
    .patient-timeline-table th { white-space: nowrap; }
    @media (max-width: 1199.98px) {
        .patient-hero,
        .patient-grid-two,
        .patient-stat-grid,
        .patient-alerts { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 767.98px) {
        .patient-info-grid { grid-template-columns: minmax(0, 1fr); }
        .patient-actions { justify-content: stretch; }
        .patient-actions .btn { width: 100%; }
    }
</style>

<div class="patient-profile">
    <section class="patient-hero">
        <div>
            <span class="patient-badge">{{ $isAr ? 'المرجع الطبي الشامل' : 'Comprehensive Medical Profile' }}</span>
            <h1 class="patient-name">{{ $patient->full_name }}</h1>
            <div class="patient-meta">
                <span>{{ $isAr ? 'الهاتف' : 'Phone' }}: {{ $patient->phone }}</span>
                <span>{{ $isAr ? 'رقم الملف' : 'File ID' }}: #{{ $patient->id }}</span>
                <span>{{ $isAr ? 'آخر زيارة' : 'Last Visit' }}: {{ $lastVisit?->visit_date?->format('Y-m-d') ?? '-' }}</span>
                <span>{{ $isAr ? 'الموعد القادم' : 'Next Appointment' }}: {{ $nextAppointment?->preferred_date?->format('Y-m-d') ?? '-' }}</span>
            </div>
            <p class="mt-3 mb-0 text-white-50">
                {{ $isAr ? 'هذه الصفحة هي المرجع الرئيسي للطبيب: تاريخ الحجز، الزيارات، الأشعة، التحاليل، الأدوية، والإجراءات في مكان واحد.' : 'This page acts as the doctor reference for appointments, visits, radiology, labs, medications, and procedures in one place.' }}
            </p>
        </div>

        <div class="patient-actions">
            <a class="btn btn-light" href="{{ route('admin.patients.index', app()->getLocale()) }}">{{ $isAr ? 'عودة للمرضى' : 'Back to Patients' }}</a>
            <a class="btn btn-outline-light" href="{{ route('admin.patients.edit', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'تعديل البيانات' : 'Edit Data' }}</a>
            <a class="btn btn-outline-light" target="_blank" href="{{ route('admin.patients.report', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'طباعة التقرير' : 'Print Report' }}</a>
            <a class="btn btn-primary" href="{{ route('admin.patients.visits.create', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'إضافة زيارة' : 'Add Visit' }}</a>
            <a class="btn btn-dark" href="{{ route('admin.patients.attachments.zip', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'تحميل مرفقات الزيارات' : 'Download Visit Attachments' }}</a>
            <a class="btn btn-secondary" href="{{ route('admin.patients.documents.medical-zip', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'ZIP الأشعة والتحاليل' : 'X-Ray & Lab ZIP' }}</a>
        </div>
    </section>

    <section class="patient-stat-grid">
        <div class="patient-stat-card">
            <small>{{ $isAr ? 'إجمالي الزيارات' : 'Total Visits' }}</small>
            <strong>{{ $patient->visits->count() }}</strong>
        </div>
        <div class="patient-stat-card">
            <small>{{ $isAr ? 'إجمالي الحجوزات' : 'Total Appointments' }}</small>
            <strong>{{ $patient->appointments->count() }}</strong>
        </div>
        <div class="patient-stat-card">
            <small>{{ $isAr ? 'الملفات الطبية' : 'Medical Documents' }}</small>
            <strong>{{ $documentStats['all'] }}</strong>
        </div>
        <div class="patient-stat-card">
            <small>{{ $isAr ? 'الأدوية المسجلة' : 'Medication Entries' }}</small>
            <strong>{{ $medicationHistory->count() }}</strong>
        </div>
    </section>

    @if($clinicalAlerts->isNotEmpty())
        <section class="patient-panel">
            <div class="patient-panel-head">
                <h5>{{ $isAr ? 'تنبيهات سريرية للطبيب' : 'Clinical Alerts for Doctor' }}</h5>
                <span class="badge text-bg-danger">{{ $clinicalAlerts->count() }}</span>
            </div>
            <div class="patient-panel-body">
                <div class="patient-alerts">
                    @foreach($clinicalAlerts as $alert)
                        <div class="patient-alert">
                            <strong>{{ $isAr ? $alert['title_ar'] : $alert['title_en'] }}</strong>
                            <div>{{ $alert['value'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="patient-grid-two">
        <div class="patient-panel">
            <div class="patient-panel-head">
                <h5>{{ $isAr ? 'البيانات الأساسية' : 'Core Patient Data' }}</h5>
            </div>
            <div class="patient-panel-body">
                <div class="patient-info-grid">
                    <div class="patient-info-item"><span>{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</span>{{ $patient->email ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'النوع' : 'Gender' }}</span>{{ $patient->gender ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'تاريخ الميلاد' : 'Date of Birth' }}</span>{{ optional($patient->date_of_birth)->format('Y-m-d') ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'فصيلة الدم' : 'Blood Type' }}</span>{{ $patient->blood_type ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'الرقم القومي' : 'National ID' }}</span>{{ $patient->national_id ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'المهنة' : 'Occupation' }}</span>{{ $patient->occupation ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'الحالة الاجتماعية' : 'Marital Status' }}</span>{{ $patient->marital_status ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'التأمين' : 'Insurance' }}</span>{{ $patient->insurance_company ?: '-' }}{{ $patient->insurance_number ? ' - '.$patient->insurance_number : '' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'جهة اتصال الطوارئ' : 'Emergency Contact' }}</span>{{ $patient->emergency_contact_name ?: '-' }}</div>
                    <div class="patient-info-item"><span>{{ $isAr ? 'هاتف الطوارئ' : 'Emergency Phone' }}</span>{{ $patient->emergency_contact_phone ?: '-' }}</div>
                    <div class="patient-info-item" style="grid-column: 1 / -1;"><span>{{ $isAr ? 'العنوان' : 'Address' }}</span>{{ $patient->address ?: '-' }}</div>
                    <div class="patient-info-item" style="grid-column: 1 / -1;"><span>{{ $isAr ? 'ملاحظات عامة' : 'General Notes' }}</span>{{ $patient->notes ?: '-' }}</div>
                </div>
            </div>
        </div>

        <div class="patient-panel">
            <div class="patient-panel-head">
                <h5>{{ $isAr ? 'ملخص علاجي سريع' : 'Quick Treatment Summary' }}</h5>
            </div>
            <div class="patient-panel-body">
                <div class="patient-list">
                    <div class="patient-list-card">
                        <strong>{{ $isAr ? 'آخر تشخيص مسجل' : 'Latest Diagnosis' }}</strong>
                        <div>{{ $lastVisit?->diagnosis ?: '-' }}</div>
                    </div>
                    <div class="patient-list-card">
                        <strong>{{ $isAr ? 'آخر خطة علاج' : 'Latest Treatment Plan' }}</strong>
                        <div>{{ $lastVisit?->treatment_plan ?: '-' }}</div>
                    </div>
                    <div class="patient-list-card">
                        <strong>{{ $isAr ? 'آخر إجراء تم' : 'Latest Procedure' }}</strong>
                        <div>{{ $lastVisit?->procedure_done ?: '-' }}</div>
                    </div>
                    <div class="patient-list-card">
                        <strong>{{ $isAr ? 'آخر روشتة / دواء' : 'Latest Prescription / Medication' }}</strong>
                        <div>{{ $medicationHistory->first()?->prescription ?: ($patient->current_medications ?: '-') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="patient-grid-two">
        <div class="patient-panel">
            <div class="patient-panel-head">
                <h5>{{ $isAr ? 'سجل الأدوية' : 'Medication History' }}</h5>
                <span class="badge text-bg-light">{{ $medicationHistory->count() }}</span>
            </div>
            <div class="patient-panel-body">
                <div class="patient-list">
                    @forelse($medicationHistory->take(8) as $visit)
                        <div class="patient-list-card">
                            <strong>{{ optional($visit->visit_date)->format('Y-m-d') ?: '-' }}</strong>
                            <div>{{ $visit->prescription }}</div>
                            <small>{{ $visit->doctor?->name ?? '-' }} | {{ $visit->branch?->name ?? '-' }}</small>
                        </div>
                    @empty
                        <div class="text-muted">{{ $isAr ? 'لا توجد أدوية مسجلة داخل الزيارات حتى الآن.' : 'No prescription entries recorded yet.' }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="patient-panel">
            <div class="patient-panel-head">
                <h5>{{ $isAr ? 'سجل الإجراءات والعلاج' : 'Procedures & Treatment Log' }}</h5>
                <span class="badge text-bg-light">{{ $procedureHistory->count() }}</span>
            </div>
            <div class="patient-panel-body">
                <div class="patient-list">
                    @forelse($procedureHistory->take(8) as $visit)
                        <div class="patient-list-card">
                            <strong>{{ optional($visit->visit_date)->format('Y-m-d') ?: '-' }}</strong>
                            <div><strong>{{ $isAr ? 'التشخيص:' : 'Diagnosis:' }}</strong> {{ $visit->diagnosis ?: '-' }}</div>
                            <div><strong>{{ $isAr ? 'الخطة:' : 'Plan:' }}</strong> {{ $visit->treatment_plan ?: '-' }}</div>
                            <div><strong>{{ $isAr ? 'الإجراء:' : 'Procedure:' }}</strong> {{ $visit->procedure_done ?: '-' }}</div>
                        </div>
                    @empty
                        <div class="text-muted">{{ $isAr ? 'لا توجد إجراءات علاجية مسجلة حتى الآن.' : 'No treatment procedures recorded yet.' }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="patient-grid-two">
        <div class="patient-panel">
            <div class="patient-panel-head">
                <h5>{{ $isAr ? 'إضافة ملف طبي' : 'Upload Medical Document' }}</h5>
            </div>
            <div class="patient-panel-body">
                <form method="POST" action="{{ route('admin.patients.documents.store', [app()->getLocale(), $patient->id]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'نوع الملف' : 'Type' }}</label>
                            <select class="form-select" name="document_type" required>
                                <option value="xray">{{ $isAr ? 'أشعة' : 'X-ray' }}</option>
                                <option value="lab">{{ $isAr ? 'تحليل' : 'Lab' }}</option>
                                <option value="report">{{ $isAr ? 'تقرير' : 'Report' }}</option>
                                <option value="prescription">{{ $isAr ? 'روشتة' : 'Prescription' }}</option>
                                <option value="other">{{ $isAr ? 'أخرى' : 'Other' }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'عنوان الملف' : 'Title' }}</label>
                            <input class="form-control" type="text" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'زيارة مرتبطة' : 'Related Visit' }}</label>
                            <select class="form-select" name="patient_visit_id">
                                <option value="">{{ $isAr ? 'بدون' : 'None' }}</option>
                                @foreach($patient->visits as $visit)
                                    <option value="{{ $visit->id }}">#{{ $visit->id }} - {{ optional($visit->visit_date)->format('Y-m-d') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'تاريخ المستند' : 'Document Date' }}</label>
                            <input class="form-control" type="date" name="document_date">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ $isAr ? 'الملف' : 'File' }}</label>
                            <input class="form-control" type="file" name="file" accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ $isAr ? 'ملاحظات' : 'Notes' }}</label>
                            <textarea class="form-control" rows="3" name="notes"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-success w-100">{{ $isAr ? 'رفع الملف' : 'Upload Document' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="patient-panel">
            <div class="patient-panel-head">
                <div>
                    <h5>{{ $isAr ? 'الأشعة والتحاليل والملفات' : 'Radiology, Lab & Medical Files' }}</h5>
                    <small class="text-muted">{{ $isAr ? 'مرجع سريع لكل ما يخص المريض' : 'Quick reference for all patient files' }}</small>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge text-bg-light">X-Ray {{ $documentStats['xray'] }}</span>
                    <span class="badge text-bg-light">Lab {{ $documentStats['lab'] }}</span>
                    <span class="badge text-bg-light">{{ $isAr ? 'تقارير' : 'Reports' }} {{ $documentStats['report'] }}</span>
                </div>
            </div>
            <div class="patient-panel-body">
                <form class="row g-2 mb-3" method="GET" action="{{ route('admin.patients.show', [app()->getLocale(), $patient->id]) }}">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" name="doc_type">
                            <option value="">{{ $isAr ? 'كل الأنواع' : 'All Types' }}</option>
                            <option value="xray" @selected(request('doc_type') === 'xray')>{{ $isAr ? 'أشعة' : 'X-ray' }}</option>
                            <option value="lab" @selected(request('doc_type') === 'lab')>{{ $isAr ? 'تحاليل' : 'Lab' }}</option>
                            <option value="report" @selected(request('doc_type') === 'report')>{{ $isAr ? 'تقارير' : 'Reports' }}</option>
                            <option value="prescription" @selected(request('doc_type') === 'prescription')>{{ $isAr ? 'روشتات' : 'Prescriptions' }}</option>
                            <option value="other" @selected(request('doc_type') === 'other')>{{ $isAr ? 'أخرى' : 'Other' }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control form-control-sm" type="text" name="doc_q" value="{{ request('doc_q') }}" placeholder="{{ $isAr ? 'بحث بالعنوان أو الملاحظة' : 'Search title or note' }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary w-100">{{ $isAr ? 'تطبيق' : 'Apply' }}</button>
                        <a class="btn btn-sm btn-outline-secondary w-100" href="{{ route('admin.patients.show', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'إعادة' : 'Reset' }}</a>
                    </div>
                </form>

                @if($documents->isEmpty())
                    <div class="text-muted">{{ $isAr ? 'لا توجد ملفات طبية مضافة حتى الآن.' : 'No medical files uploaded yet.' }}</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle patient-timeline-table">
                            <thead>
                            <tr>
                                <th>{{ $isAr ? 'النوع' : 'Type' }}</th>
                                <th>{{ $isAr ? 'العنوان' : 'Title' }}</th>
                                <th>{{ $isAr ? 'الزيارة' : 'Visit' }}</th>
                                <th>{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                                <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($documents as $doc)
                                <tr>
                                    <td><span class="badge text-bg-light">{{ $doc->document_type }}</span></td>
                                    <td>
                                        <div class="fw-semibold">{{ $doc->title }}</div>
                                        <small class="text-muted">{{ $doc->notes ?: '-' }}</small>
                                    </td>
                                    <td>{{ $doc->visit?->visit_date?->format('Y-m-d') ?: '-' }}</td>
                                    <td>{{ optional($doc->document_date)->format('Y-m-d') ?: '-' }}</td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/' . ltrim($doc->file_path, '/')) }}">{{ $isAr ? 'فتح' : 'Open' }}</a>
                                        <form class="d-inline" method="POST" action="{{ route('admin.patients.documents.destroy', [app()->getLocale(), $patient->id, $doc->id]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف الملف؟' : 'Delete file?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $documents->links() }}
                @endif
            </div>
        </div>
    </section>

    <section class="patient-panel">
        <div class="patient-panel-head">
            <h5>{{ $isAr ? 'سجل الحجوزات' : 'Appointments History' }}</h5>
            <span class="badge text-bg-light">{{ $patient->appointments->count() }}</span>
        </div>
        <div class="patient-panel-body">
            <div class="table-responsive">
                <table class="table align-middle patient-timeline-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ $isAr ? 'التاريخ والوقت' : 'Date & Time' }}</th>
                        <th>{{ $isAr ? 'الخدمة / الفرع' : 'Service / Branch' }}</th>
                        <th>{{ $isAr ? 'الحالة' : 'Status' }}</th>
                        <th>{{ $isAr ? 'التحويل لزيارة' : 'Converted to Visit' }}</th>
                        <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($patient->appointments as $appointment)
                        <tr>
                            <td>#{{ $appointment->id }}</td>
                            <td>
                                <div>{{ optional($appointment->preferred_date)->format('Y-m-d') ?: '-' }}</div>
                                <small class="text-muted">{{ $appointment->preferred_time ?: '-' }}</small>
                            </td>
                            <td>
                                <div>{{ $appointment->service?->title ?? '-' }}</div>
                                <small class="text-muted">{{ $appointment->branch?->name ?? '-' }}</small>
                            </td>
                            <td><span class="badge text-bg-light">{{ $appointment->status }}</span></td>
                            <td>
                                @if($appointment->visit)
                                    <span class="badge text-bg-success">{{ $isAr ? 'نعم' : 'Yes' }}</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $isAr ? 'لا' : 'No' }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.appointments.show', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'عرض الحجز' : 'View Appointment' }}</a>
                                @if($appointment->visit)
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('admin.patients.visits.edit', [app()->getLocale(), $patient->id, $appointment->visit->id]) }}">{{ $isAr ? 'الزيارة' : 'Visit' }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">{{ $isAr ? 'لا توجد حجوزات مرتبطة بعد.' : 'No linked appointments yet.' }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="patient-panel">
        <div class="patient-panel-head">
            <h5>{{ $isAr ? 'سجل الزيارات والتشخيص والعلاج' : 'Visits, Diagnosis & Treatment Timeline' }}</h5>
            <span class="badge text-bg-light">{{ $patient->visits->count() }}</span>
        </div>
        <div class="patient-panel-body">
            <div class="table-responsive">
                <table class="table align-middle patient-timeline-table">
                    <thead>
                    <tr>
                        <th>{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                        <th>{{ $isAr ? 'الطبيب / الفرع' : 'Doctor / Branch' }}</th>
                        <th>{{ $isAr ? 'الشكوى / التشخيص' : 'Complaint / Diagnosis' }}</th>
                        <th>{{ $isAr ? 'الخطة والإجراء' : 'Plan & Procedure' }}</th>
                        <th>{{ $isAr ? 'الأدوية والمتابعة' : 'Medication & Follow-up' }}</th>
                        <th>{{ $isAr ? 'المرفقات' : 'Attachments' }}</th>
                        <th>{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($patient->visits as $visit)
                        <tr>
                            <td class="text-nowrap">
                                <div>{{ optional($visit->visit_date)->format('Y-m-d') ?: '-' }}</div>
                                <small class="text-muted">{{ $visit->visit_time ?: '-' }} | {{ $visit->visit_status }}</small>
                            </td>
                            <td>
                                <div>{{ $visit->doctor?->name ?? '-' }}</div>
                                <small class="text-muted">{{ $visit->branch?->name ?? '-' }}</small>
                            </td>
                            <td>
                                <div><strong>{{ $isAr ? 'الشكوى:' : 'Complaint:' }}</strong> {{ $visit->chief_complaint ?: '-' }}</div>
                                <div><strong>{{ $isAr ? 'التشخيص:' : 'Diagnosis:' }}</strong> {{ $visit->diagnosis ?: '-' }}</div>
                            </td>
                            <td>
                                <div><strong>{{ $isAr ? 'الخطة:' : 'Plan:' }}</strong> {{ $visit->treatment_plan ?: '-' }}</div>
                                <div><strong>{{ $isAr ? 'الإجراء:' : 'Procedure:' }}</strong> {{ $visit->procedure_done ?: '-' }}</div>
                            </td>
                            <td>
                                <div><strong>{{ $isAr ? 'الروشتة:' : 'Prescription:' }}</strong> {{ $visit->prescription ?: '-' }}</div>
                                <small class="text-muted">{{ $isAr ? 'المتابعة القادمة:' : 'Next Visit:' }} {{ optional($visit->next_visit_date)->format('Y-m-d') ?: '-' }}</small>
                            </td>
                            <td>
                                @if($visit->attachments->isEmpty())
                                    <span class="text-muted">-</span>
                                @else
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($visit->attachments as $attachment)
                                            <div>
                                                <a target="_blank" href="{{ asset('storage/' . ltrim($attachment->file_path, '/')) }}">{{ $attachment->file_name }}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.patients.visits.edit', [app()->getLocale(), $patient->id, $visit->id]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.patients.visits.destroy', [app()->getLocale(), $patient->id, $visit->id]) }}" onsubmit="return confirm('{{ $isAr ? 'حذف الزيارة؟' : 'Delete this visit?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">{{ $isAr ? 'حذف' : 'Delete' }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">{{ $isAr ? 'لا توجد زيارات مسجلة بعد.' : 'No visits recorded yet.' }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
