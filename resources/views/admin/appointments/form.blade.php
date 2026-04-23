@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')
@php($isEdit = isset($appointment))
@php($patientProfile = $appointment->patient ?? null)

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">
        {{ $isEdit ? ($isAr ? 'تعديل الحجز' : 'Edit Appointment') : ($isAr ? 'إنشاء حجز جديد' : 'Create Appointment') }}
    </h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.appointments.index', app()->getLocale()) }}">
        {{ $isAr ? 'عودة للحجوزات' : 'Back to list' }}
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $isEdit ? route('admin.appointments.update', [app()->getLocale(), $appointment->id]) : route('admin.appointments.store', app()->getLocale()) }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'اسم المريض' : 'Patient name' }}</label>
                    <input class="form-control" name="patient_name" value="{{ old('patient_name', $appointment->patient_name ?? '') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'رقم الهاتف' : 'Phone' }}</label>
                    <input class="form-control" name="patient_phone" value="{{ old('patient_phone', $appointment->patient_phone ?? '') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الحالة' : 'Status' }}</label>
                    <select class="form-select" name="status" required>
                        <option value="new" @selected(old('status', $appointment->status ?? 'new') === 'new')>{{ $isAr ? 'جديد' : 'New' }}</option>
                        <option value="contacted" @selected(old('status', $appointment->status ?? '') === 'contacted')>{{ $isAr ? 'تم التواصل' : 'Contacted' }}</option>
                        <option value="completed" @selected(old('status', $appointment->status ?? '') === 'completed')>{{ $isAr ? 'مكتمل' : 'Completed' }}</option>
                        <option value="canceled" @selected(old('status', $appointment->status ?? '') === 'canceled')>{{ $isAr ? 'ملغي' : 'Canceled' }}</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
                    <select class="form-select" name="branch_id">
                        <option value="">{{ $isAr ? 'اختر الفرع' : 'Select branch' }}</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" @selected((string) old('branch_id', $appointment->branch_id ?? '') === (string) $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الخدمة' : 'Service' }}</label>
                    <select class="form-select" name="service_id">
                        <option value="">{{ $isAr ? 'اختر الخدمة' : 'Select service' }}</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}" @selected((string) old('service_id', $appointment->service_id ?? '') === (string) $s->id)>{{ $s->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'تاريخ الحجز' : 'Preferred date' }}</label>
                    <input type="date" class="form-control" name="preferred_date" value="{{ old('preferred_date', isset($appointment) ? $appointment->preferred_date : now()->toDateString()) }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'الوقت' : 'Preferred time' }}</label>
                    <input type="time" class="form-control" name="preferred_time" value="{{ old('preferred_time', isset($appointment) ? $appointment->preferred_time : '10:00') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'مصدر الحجز' : 'Source' }}</label>
                    <select class="form-select" name="source">
                        <option value="website" @selected(old('source', $appointment->source ?? 'website') === 'website')>{{ $isAr ? 'الموقع' : 'Website' }}</option>
                        <option value="whatsapp" @selected(old('source', $appointment->source ?? '') === 'whatsapp')>{{ $isAr ? 'واتساب' : 'WhatsApp' }}</option>
                        <option value="phone" @selected(old('source', $appointment->source ?? '') === 'phone')>{{ $isAr ? 'اتصال هاتفي' : 'Phone' }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'نوع الحجز' : 'Booking Type' }}</label>
                    <select class="form-select" name="booking_type" id="booking_type" required>
                        <option value="regular" @selected(old('booking_type', $appointment->booking_type ?? 'regular') === 'regular')>{{ $isAr ? 'عادي' : 'Regular' }}</option>
                        <option value="vip" @selected(old('booking_type', $appointment->booking_type ?? '') === 'vip')>VIP</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ $isAr ? 'السعر' : 'Price' }}</label>
                    <input type="number" step="0.01" min="0" class="form-control" name="price" id="price" value="{{ old('price', $appointment->price ?? '') }}">
                    <small class="text-muted">{{ $isAr ? 'يتغير تلقائيًا حسب النوع ويمكن تعديله.' : 'Auto-fills by booking type and can be edited.' }}</small>
                </div>
                <div class="col-md-12">
                    <label class="form-label">{{ $isAr ? 'ملاحظات الحجز' : 'Notes' }}</label>
                    <textarea class="form-control" rows="3" name="notes" placeholder="{{ $isAr ? 'أعراض، تفاصيل تواصل، ملاحظات للطبيب...' : 'Symptoms, contact notes, doctor notes...' }}">{{ old('notes', $appointment->notes ?? '') }}</textarea>
                </div>

                <div class="col-12 mt-2">
                    <hr>
                    <h5 class="mb-3">{{ $isAr ? 'نموذج الاستقبال الأولي' : 'Initial Intake Form' }}</h5>
                    <p class="text-muted small mb-3">{{ $isAr ? 'هذه البيانات تُحفظ مباشرة داخل ملف المريض وتظهر للطبيب من أول حجز.' : 'These details are saved directly into the patient profile and shown to the doctor from the first booking.' }}</p>
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $patientProfile->email ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'تاريخ الميلاد' : 'Date of Birth' }}</label>
                    <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth', isset($patientProfile?->date_of_birth) ? optional($patientProfile->date_of_birth)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'النوع' : 'Gender' }}</label>
                    <select class="form-select" name="gender">
                        <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                        <option value="male" @selected(old('gender', $patientProfile->gender ?? '') === 'male')>{{ $isAr ? 'ذكر' : 'Male' }}</option>
                        <option value="female" @selected(old('gender', $patientProfile->gender ?? '') === 'female')>{{ $isAr ? 'أنثى' : 'Female' }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'المهنة' : 'Occupation' }}</label>
                    <input class="form-control" name="occupation" value="{{ old('occupation', $patientProfile->occupation ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'الحالة الاجتماعية' : 'Marital Status' }}</label>
                    <input class="form-control" name="marital_status" value="{{ old('marital_status', $patientProfile->marital_status ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'فصيلة الدم' : 'Blood Type' }}</label>
                    <input class="form-control" name="blood_type" value="{{ old('blood_type', $patientProfile->blood_type ?? '') }}" placeholder="A+">
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'اسم جهة اتصال الطوارئ' : 'Emergency Contact Name' }}</label>
                    <input class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patientProfile->emergency_contact_name ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'هاتف الطوارئ' : 'Emergency Contact Phone' }}</label>
                    <input class="form-control" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patientProfile->emergency_contact_phone ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'العنوان' : 'Address' }}</label>
                    <textarea class="form-control" rows="2" name="address">{{ old('address', $patientProfile->address ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'الحساسية' : 'Allergies' }}</label>
                    <textarea class="form-control" rows="2" name="allergies">{{ old('allergies', $patientProfile->allergies ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'الأمراض المزمنة' : 'Chronic Diseases' }}</label>
                    <textarea class="form-control" rows="2" name="chronic_diseases">{{ old('chronic_diseases', $patientProfile->chronic_diseases ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'الأدوية الحالية' : 'Current Medications' }}</label>
                    <textarea class="form-control" rows="2" name="current_medications">{{ old('current_medications', $patientProfile->current_medications ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'عمليات أو إجراءات سابقة' : 'Previous Surgeries / Procedures' }}</label>
                    <textarea class="form-control" rows="2" name="previous_surgeries">{{ old('previous_surgeries', $patientProfile->previous_surgeries ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ $isAr ? 'حالة التدخين' : 'Smoking Status' }}</label>
                    <input class="form-control" name="smoking_status" value="{{ old('smoking_status', $patientProfile->smoking_status ?? '') }}" placeholder="{{ $isAr ? 'مثال: غير مدخن' : 'Example: Non-smoker' }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a class="btn btn-outline-secondary" href="{{ route('admin.appointments.index', app()->getLocale()) }}">{{ $isAr ? 'إلغاء' : 'Cancel' }}</a>
                <button class="btn btn-success">{{ $isAr ? 'حفظ الحجز' : 'Save Appointment' }}</button>
            </div>
        </form>
    </div>
</div>
<script>
    (function () {
        var bookingType = document.getElementById('booking_type');
        var priceInput = document.getElementById('price');
        var regularPrice = {{ (float) ($regularPrice ?? 300) }};
        var vipPrice = {{ (float) ($vipPrice ?? 600) }};

        if (!bookingType || !priceInput) {
            return;
        }

        function setDefaultPrice() {
            if (priceInput.value !== '' && !priceInput.dataset.autofill) {
                return;
            }
            priceInput.dataset.autofill = '1';
            priceInput.value = bookingType.value === 'vip' ? vipPrice : regularPrice;
        }

        if (priceInput.value === '') {
            setDefaultPrice();
        }

        bookingType.addEventListener('change', function () {
            priceInput.dataset.autofill = '1';
            setDefaultPrice();
        });
    })();
</script>
@endsection
