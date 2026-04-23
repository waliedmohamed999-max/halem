@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';

    $visitTypes = [
        'first_visit' => $isAr ? 'أول زيارة' : 'First Visit',
        'follow_up' => $isAr ? 'متابعة' : 'Follow-up',
        'emergency' => $isAr ? 'حالة طوارئ' : 'Emergency',
        'consultation' => $isAr ? 'استشارة' : 'Consultation',
    ];

    $symptomOptions = [
        'tooth_pain' => $isAr ? 'ألم أسنان' : 'Tooth Pain',
        'gum_bleeding' => $isAr ? 'نزيف لثة' : 'Gum Bleeding',
        'swelling' => $isAr ? 'تورم' : 'Swelling',
        'sensitivity' => $isAr ? 'حساسية' : 'Sensitivity',
        'bad_breath' => $isAr ? 'رائحة فم' : 'Bad Breath',
        'checkup' => $isAr ? 'فحص دوري' : 'Routine Checkup',
    ];

    $availabilityUrl = route('front.appointments.availability', app()->getLocale());
@endphp

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    .booking-shell {
        border: 1px solid #d7e4f2;
        border-radius: 1.6rem;
        background: linear-gradient(180deg, #eef6ff 0%, #f9fbff 100%);
        padding: 1.1rem;
        box-shadow: 0 18px 40px rgba(18, 55, 96, .08);
    }
    .booking-hero {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1.15rem 1.2rem;
        border: 1px solid #d7e4f2;
        border-radius: 1.35rem;
        background:
            radial-gradient(circle at top right, rgba(69, 136, 219, .14), transparent 28%),
            linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
    }
    .booking-hero h2 {
        margin: 0 0 .3rem;
        color: #123e6d;
        font-size: clamp(1.55rem, 1.3rem + .85vw, 2.1rem);
        font-weight: 900;
    }
    .booking-hero p {
        margin: 0;
        max-width: 62ch;
        color: #607a95;
        line-height: 1.9;
    }
    .booking-badge-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: end;
        gap: .55rem;
    }
    .booking-badge {
        border: 1px solid #c7daf0;
        background: #eef6ff;
        color: #1f4d8b;
        border-radius: 999px;
        padding: .32rem .78rem;
        font-size: .82rem;
        font-weight: 700;
    }
    .booking-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.85fr) minmax(290px, .95fr);
        gap: 1rem;
        align-items: start;
    }
    .booking-panel {
        border: 1px solid #dbe6f2;
        border-radius: 1.2rem;
        background: #fff;
        padding: 1.05rem;
        box-shadow: 0 12px 26px rgba(17, 53, 94, .06);
    }
    .section-title {
        margin-bottom: .85rem;
        color: #1f4d8b;
        font-weight: 800;
    }
    .booking-subcard {
        border: 1px solid #d8e5f2;
        border-radius: 1rem;
        background: linear-gradient(180deg, #fcfeff 0%, #f7fbff 100%);
        padding: .95rem;
    }
    .booking-subcard + .booking-subcard {
        margin-top: .85rem;
    }
    .subcard-title {
        margin: 0 0 .85rem;
        color: #123d68;
        font-size: 1rem;
        font-weight: 800;
    }
    .form-label {
        margin-bottom: .38rem;
        color: #143f6d;
        font-size: .88rem;
        font-weight: 700;
    }
    .form-control,
    .form-select {
        min-height: 44px;
        border-color: #d5e2f1;
        border-radius: .85rem;
        box-shadow: none;
    }
    .form-control:focus,
    .form-select:focus {
        border-color: #7eaadb;
        box-shadow: 0 0 0 .2rem rgba(58, 125, 205, .12);
    }
    .calendar-box {
        border: 1px solid #d9e5f2;
        border-radius: .95rem;
        background: #f9fcff;
        padding: .75rem;
    }
    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
        gap: .45rem;
    }
    .slot-btn {
        border: 1px solid #c5d8ef;
        background: #fff;
        color: #1f4d8b;
        border-radius: .7rem;
        padding: .42rem .35rem;
        font-size: .82rem;
        cursor: pointer;
    }
    .slot-btn:hover {
        border-color: #7fa9d3;
    }
    .slot-btn.active {
        background: #1d73e8;
        color: #fff;
        border-color: #1d73e8;
    }
    .slot-btn.booked {
        background: #f1f5f9;
        color: #9aa7b7;
        border-color: #d6dee8;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .pain-value {
        font-weight: 800;
        color: #1f4d8b;
    }
    .symptom-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .65rem;
    }
    .symptom-check {
        position: relative;
    }
    .symptom-check .form-check-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .symptom-pill {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .55rem;
        min-height: 48px;
        padding: .72rem .8rem;
        border: 1px solid #d6e4f2;
        border-radius: .9rem;
        background: #fbfdff;
        color: #4c657f;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .symptom-pill::before {
        content: '';
        width: 1rem;
        height: 1rem;
        border: 1px solid #bfd2e6;
        border-radius: 999px;
        background: #fff;
        flex-shrink: 0;
    }
    .symptom-check .form-check-input:checked + .symptom-pill {
        border-color: #5d96d2;
        background: #eef6ff;
        color: #14467a;
        box-shadow: 0 10px 20px rgba(34, 96, 164, .08);
    }
    .symptom-check .form-check-input:checked + .symptom-pill::before {
        background: linear-gradient(135deg, #2b7be0, #1c67cc);
        border-color: #2b7be0;
        box-shadow: inset 0 0 0 3px #fff;
    }
    .notes-box {
        margin-top: .9rem;
    }
    .mini-card {
        border: 1px dashed #c4d8ed;
        border-radius: .9rem;
        padding: .8rem;
        background: #f9fcff;
    }
    .mini-card + .mini-card {
        margin-top: .65rem;
    }
    .helper-actions {
        display: grid;
        gap: .6rem;
        margin-top: .75rem;
    }
    .helper-actions .btn {
        min-height: 44px;
        border-radius: .85rem;
        font-weight: 700;
    }
    .working-hours-list {
        display: grid;
        gap: .45rem;
    }
    .work-hour-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding: .55rem .72rem;
        border: 1px solid #dce7f3;
        border-radius: .8rem;
        background: #fbfdff;
        font-size: .88rem;
    }
    .submit-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .8rem;
        margin-top: 1rem;
        padding-top: .85rem;
        border-top: 1px dashed #d8e5f2;
    }
    @media (max-width: 991.98px) {
        .booking-layout,
        .symptom-grid {
            grid-template-columns: 1fr;
        }
        .booking-hero {
            flex-direction: column;
        }
        .booking-badge-group {
            justify-content: start;
        }
    }
    @media (max-width: 767.98px) {
        .submit-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .submit-bar .btn {
            width: 100%;
        }
    }
</style>

<div class="booking-shell mb-4">
    <div class="booking-hero">
        <div>
            <h2>{{ $isAr ? 'احجز موعدك بسهولة' : 'Book Appointment' }}</h2>
            <p>{{ $isAr ? 'أدخل بيانات الحجز الأساسية، ثم اختر الفرع والتاريخ لمشاهدة المواعيد المتاحة والمحجوزة بشكل واضح ومباشر.' : 'Enter the core appointment details, then choose branch and date to view available and booked slots clearly in real time.' }}</p>
        </div>
        <div class="booking-badge-group">
            <span class="booking-badge">{{ $isAr ? 'تأكيد سريع' : 'Fast Confirmation' }}</span>
            <span class="booking-badge">{{ $isAr ? 'منع التكرار' : 'No Double Booking' }}</span>
        </div>
    </div>

    <div class="booking-layout">
        <div class="booking-panel">
            <h5 class="section-title">{{ $isAr ? 'بيانات الحجز' : 'Appointment Details' }}</h5>

            <form method="POST" action="{{ route('front.appointments.store', app()->getLocale()) }}" id="appointmentForm">
                @csrf

                <div class="booking-subcard">
                    <h6 class="subcard-title">{{ $isAr ? 'البيانات الأساسية' : 'Core Details' }}</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'اسم المريض' : 'Patient Name' }}</label>
                            <input class="form-control" name="patient_name" value="{{ old('patient_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'رقم الهاتف' : 'Phone Number' }}</label>
                            <input class="form-control" name="patient_phone" value="{{ old('patient_phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'الفرع' : 'Branch' }}</label>
                            <select class="form-select" name="branch_id" id="branch_id" required>
                                <option value="">{{ $isAr ? 'اختر الفرع' : 'Select Branch' }}</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" @selected((string) old('branch_id') === (string) $b->id)>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'الخدمة' : 'Service' }}</label>
                            <select class="form-select" name="service_id" required>
                                <option value="">{{ $isAr ? 'اختر الخدمة' : 'Select Service' }}</option>
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}" @selected((string) old('service_id') === (string) $s->id)>{{ $s->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'نوع الحجز' : 'Booking Type' }}</label>
                            <select class="form-select" name="booking_type" id="booking_type" required>
                                <option value="regular" @selected(old('booking_type', 'regular') === 'regular')>{{ $isAr ? 'عادي' : 'Regular' }}</option>
                                <option value="vip" @selected(old('booking_type') === 'vip')>VIP</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'السعر' : 'Price' }}</label>
                            <input type="number" step="0.01" min="0" class="form-control" name="price" id="price" value="{{ old('price') }}">
                            <small class="text-muted">
                                {{ $isAr ? 'السعر العادي:' : 'Regular:' }} {{ number_format((float) ($regularPrice ?? 300), 2) }}
                                | VIP: {{ number_format((float) ($vipPrice ?? 600), 2) }}
                            </small>
                            <div class="small text-danger mt-2 fw-semibold">
                                {{ $isAr ? 'ملحوظة: السعر الظاهر هو سعر الكشف فقط، وأي خدمات أو إجراءات علاجية إضافية تُحسب بشكل منفصل.' : 'Note: The displayed amount covers consultation only. Any treatment services or procedures are charged separately.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking-subcard">
                    <h6 class="subcard-title">{{ $isAr ? 'الموعد والتفضيلات' : 'Schedule & Preferences' }}</h6>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">{{ $isAr ? 'التاريخ المفضل' : 'Preferred Date' }}</label>
                            <div class="calendar-box">
                                <input type="text" class="form-control" id="preferred_date_picker" value="{{ old('preferred_date') }}" placeholder="{{ $isAr ? 'اختر التاريخ' : 'Pick date' }}" required>
                                <input type="hidden" name="preferred_date" id="preferred_date" value="{{ old('preferred_date') }}">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label d-flex justify-content-between">
                                <span>{{ $isAr ? 'الوقت المفضل' : 'Preferred Time' }}</span>
                                <span id="slotStatus" class="text-muted small">{{ $isAr ? 'اختر الفرع والتاريخ أولًا' : 'Select branch & date first' }}</span>
                            </label>
                            <div class="slot-grid" id="slotGrid"></div>
                            <input type="hidden" name="preferred_time" id="preferred_time" value="{{ old('preferred_time') }}" required>
                            <div class="small mt-1 text-muted" id="selectedTimeText"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'نوع الزيارة' : 'Visit Type' }}</label>
                            <select class="form-select" name="visit_type" id="visit_type">
                                <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                                @foreach($visitTypes as $value => $label)
                                    <option value="{{ $value }}" @selected(old('visit_type') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label d-flex justify-content-between">
                                <span>{{ $isAr ? 'مستوى الألم (0 - 10)' : 'Pain Level (0 - 10)' }}</span>
                                <span class="pain-value" id="painLevelLabel">{{ old('pain_level', 0) }}/10</span>
                            </label>
                            <input type="range" class="form-range" min="0" max="10" step="1" name="pain_level" id="pain_level" value="{{ old('pain_level', 0) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'طريقة التواصل المفضلة' : 'Preferred Contact Method' }}</label>
                            <select class="form-select" name="preferred_contact">
                                <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                                <option value="phone" @selected(old('preferred_contact') === 'phone')>{{ $isAr ? 'اتصال هاتفي' : 'Phone Call' }}</option>
                                <option value="whatsapp" @selected(old('preferred_contact') === 'whatsapp')>{{ $isAr ? 'واتساب' : 'WhatsApp' }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="booking-subcard">
                    <h6 class="subcard-title">{{ $isAr ? 'الأعراض والملاحظات' : 'Symptoms & Notes' }}</h6>
                    <label class="form-label">{{ $isAr ? 'أعراض أو سبب الزيارة' : 'Symptoms / Visit Reason' }}</label>
                    <div class="symptom-grid">
                        @foreach($symptomOptions as $value => $label)
                            <div class="symptom-check">
                                <input class="form-check-input" type="checkbox" name="symptoms[]" value="{{ $value }}" id="symptom_{{ $value }}" @checked(collect(old('symptoms', []))->contains($value))>
                                <label class="symptom-pill" for="symptom_{{ $value }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>

                    <div class="notes-box">
                        <label class="form-label">{{ $isAr ? 'ملاحظات إضافية للطبيب' : 'Additional Notes for Doctor' }}</label>
                        <textarea class="form-control" rows="4" name="notes" placeholder="{{ $isAr ? 'اكتب أي معلومات طبية مهمة أو أدوية حالية...' : 'Write any relevant medical notes or current medications...' }}">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="booking-subcard">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <strong>{{ $isAr ? 'نموذج استقبال أولي اختياري' : 'Optional Initial Intake' }}</strong>
                        <span class="booking-badge">{{ $isAr ? 'يفيد الطبيب في أول زيارة' : 'Helps the doctor at first visit' }}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'تاريخ الميلاد' : 'Date of Birth' }}</label>
                            <input type="date" class="form-control" name="date_of_birth" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'النوع' : 'Gender' }}</label>
                            <select class="form-select" name="gender">
                                <option value="">{{ $isAr ? 'اختياري' : 'Optional' }}</option>
                                <option value="male" @selected(old('gender') === 'male')>{{ $isAr ? 'ذكر' : 'Male' }}</option>
                                <option value="female" @selected(old('gender') === 'female')>{{ $isAr ? 'أنثى' : 'Female' }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'الحساسية' : 'Allergies' }}</label>
                            <textarea class="form-control" rows="2" name="allergies">{{ old('allergies') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'الأمراض المزمنة' : 'Chronic Diseases' }}</label>
                            <textarea class="form-control" rows="2" name="chronic_diseases">{{ old('chronic_diseases') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'الأدوية الحالية' : 'Current Medications' }}</label>
                            <textarea class="form-control" rows="2" name="current_medications">{{ old('current_medications') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'عمليات أو إجراءات سابقة' : 'Previous Surgeries / Procedures' }}</label>
                            <textarea class="form-control" rows="2" name="previous_surgeries">{{ old('previous_surgeries') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'المهنة' : 'Occupation' }}</label>
                            <input class="form-control" name="occupation" value="{{ old('occupation') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'الحالة الاجتماعية' : 'Marital Status' }}</label>
                            <input class="form-control" name="marital_status" value="{{ old('marital_status') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'فصيلة الدم' : 'Blood Type' }}</label>
                            <input class="form-control" name="blood_type" value="{{ old('blood_type') }}" placeholder="A+">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'اسم جهة اتصال الطوارئ' : 'Emergency Contact Name' }}</label>
                            <input class="form-control" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'هاتف الطوارئ' : 'Emergency Contact Phone' }}</label>
                            <input class="form-control" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}">
                        </div>
                    </div>
                </div>

                <div class="submit-bar">
                    <small class="text-secondary">{{ $isAr ? 'أي وقت يظهر مشطوبًا فهو محجوز بالفعل.' : 'Any crossed-out time is already booked.' }}</small>
                    <button class="btn btn-primary px-4" type="submit">{{ $isAr ? 'تأكيد الحجز' : 'Submit Appointment' }}</button>
                </div>
            </form>
        </div>

        <div>
            <div class="booking-panel mb-3">
                <h5 class="section-title">{{ $isAr ? 'مساعد المريض' : 'Patient Assistant' }}</h5>
                <div class="mini-card">
                    <strong>{{ $isAr ? 'قبل الموعد' : 'Before Visit' }}</strong>
                    <div class="small text-secondary mt-1">{{ $isAr ? 'حضّر أي أشعة أو تقارير سابقة، ودوّن الأعراض الأساسية.' : 'Bring any previous x-rays/reports and list key symptoms.' }}</div>
                </div>
                <div class="mini-card">
                    <strong>{{ $isAr ? 'وقت الانتظار المتوقع' : 'Estimated Waiting' }}</strong>
                    <div class="small text-secondary mt-1">{{ $isAr ? '10 - 20 دقيقة حسب ضغط الحالات.' : '10-20 minutes depending on queue load.' }}</div>
                </div>
                <div class="helper-actions">
                    @if($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-outline-success">{{ $isAr ? 'التواصل عبر واتساب' : 'Chat on WhatsApp' }}</a>
                    @endif
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn btn-outline-primary">{{ $isAr ? 'اتصال سريع بالمركز' : 'Quick Call' }}</a>
                </div>
            </div>

            <div class="booking-panel mb-3">
                <h5 class="section-title">{{ $isAr ? 'ساعات العمل' : 'Working Hours' }}</h5>
                <div class="working-hours-list">
                    @foreach($hours as $hour)
                        <div class="work-hour-row">
                            <span>{{ $isAr ? $hour->day_label_ar : $hour->day_label_en }}</span>
                            <span>{{ $hour->is_open ? (substr((string) $hour->open_at, 0, 5) . ' - ' . substr((string) $hour->close_at, 0, 5)) : ($isAr ? 'مغلق' : 'Closed') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($emergency)
                <div class="booking-panel border-danger">
                    <h5 class="section-title text-danger">{{ $isAr ? 'تنبيه طوارئ' : 'Emergency Notice' }}</h5>
                    <p class="small mb-2">{{ $emergency->emergency_text }}</p>
                    @if($emergency->emergency_phone)
                        <a class="btn btn-danger btn-sm" href="tel:{{ preg_replace('/[^0-9+]/', '', $emergency->emergency_phone) }}">{{ $isAr ? 'اتصال الطوارئ' : 'Emergency Call' }}</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    (function () {
        var isAr = {{ $isAr ? 'true' : 'false' }};
        var availabilityUrl = @json($availabilityUrl);
        var oldSelectedTime = @json(old('preferred_time'));

        var painRange = document.getElementById('pain_level');
        var painLabel = document.getElementById('painLevelLabel');
        var branchInput = document.getElementById('branch_id');
        var dateHiddenInput = document.getElementById('preferred_date');
        var datePickerInput = document.getElementById('preferred_date_picker');
        var timeHiddenInput = document.getElementById('preferred_time');
        var slotGrid = document.getElementById('slotGrid');
        var slotStatus = document.getElementById('slotStatus');
        var selectedTimeText = document.getElementById('selectedTimeText');
        var bookingTypeInput = document.getElementById('booking_type');
        var priceInput = document.getElementById('price');
        var regularPrice = {{ (float) ($regularPrice ?? 300) }};
        var vipPrice = {{ (float) ($vipPrice ?? 600) }};

        function setPain() {
            if (painRange && painLabel) {
                painLabel.textContent = painRange.value + '/10';
            }
        }

        function setStatus(text, isError) {
            slotStatus.textContent = text;
            slotStatus.classList.toggle('text-danger', !!isError);
        }

        function setSelectedTimeText(time) {
            if (!time) {
                selectedTimeText.textContent = '';
                return;
            }
            selectedTimeText.textContent = (isAr ? 'الوقت المختار: ' : 'Selected time: ') + time;
        }

        function syncPriceByType(force) {
            if (!bookingTypeInput || !priceInput) {
                return;
            }

            if (!force && priceInput.value !== '' && !priceInput.dataset.autofill) {
                return;
            }

            priceInput.dataset.autofill = '1';
            priceInput.value = bookingTypeInput.value === 'vip' ? vipPrice : regularPrice;
        }

        function renderSlots(slots) {
            slotGrid.innerHTML = '';
            if (!slots || !slots.length) {
                setStatus(isAr ? 'لا توجد أوقات متاحة.' : 'No available slots.', true);
                return;
            }

            var selected = timeHiddenInput.value || oldSelectedTime;
            slots.forEach(function (slot) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'slot-btn';
                btn.textContent = slot.time;

                if (slot.is_booked) {
                    btn.classList.add('booked');
                    btn.disabled = true;
                    btn.title = isAr ? 'محجوز' : 'Booked';
                } else {
                    btn.addEventListener('click', function () {
                        document.querySelectorAll('.slot-btn.active').forEach(function (el) { el.classList.remove('active'); });
                        btn.classList.add('active');
                        timeHiddenInput.value = slot.time;
                        setSelectedTimeText(slot.time);
                    });
                }

                if (!slot.is_booked && selected && selected === slot.time) {
                    btn.classList.add('active');
                    timeHiddenInput.value = slot.time;
                    setSelectedTimeText(slot.time);
                }

                slotGrid.appendChild(btn);
            });

            setStatus(isAr ? 'الأوقات المشطوبة محجوزة.' : 'Crossed times are already booked.', false);
        }

        function fetchAvailability() {
            var branchId = branchInput.value;
            var date = dateHiddenInput.value;

            timeHiddenInput.value = '';
            setSelectedTimeText('');
            slotGrid.innerHTML = '';

            if (!branchId || !date) {
                setStatus(isAr ? 'اختر الفرع والتاريخ أولًا.' : 'Select branch and date first.', false);
                return;
            }

            setStatus(isAr ? 'جاري تحميل الأوقات...' : 'Loading slots...', false);

            var url = new URL(availabilityUrl, window.location.origin);
            url.searchParams.set('branch_id', branchId);
            url.searchParams.set('date', date);

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.is_open) {
                    slotGrid.innerHTML = '';
                    setStatus(data.message || (isAr ? 'هذا اليوم مغلق.' : 'This day is closed.'), true);
                    return;
                }

                renderSlots(data.slots || []);
            })
            .catch(function () {
                setStatus(isAr ? 'حدث خطأ أثناء جلب الأوقات.' : 'Failed to load slots.', true);
            });
        }

        if (datePickerInput) {
            flatpickr(datePickerInput, {
                dateFormat: 'Y-m-d',
                minDate: 'today',
                defaultDate: dateHiddenInput.value || null,
                onChange: function (selectedDates, dateStr) {
                    dateHiddenInput.value = dateStr;
                    fetchAvailability();
                }
            });
        }

        if (branchInput) {
            branchInput.addEventListener('change', fetchAvailability);
        }
        if (bookingTypeInput) {
            bookingTypeInput.addEventListener('change', function () { syncPriceByType(true); });
        }
        if (painRange) {
            painRange.addEventListener('input', setPain);
        }

        setPain();
        syncPriceByType(false);

        if (branchInput && branchInput.value && dateHiddenInput.value) {
            fetchAvailability();
        } else {
            setStatus(isAr ? 'اختر الفرع والتاريخ أولًا.' : 'Select branch and date first.', false);
        }
    })();
</script>
@endsection
