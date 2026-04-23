@extends('layouts.front')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-search-heart"></i> {{ $isAr ? 'متابعة الحجز' : 'Track appointment' }}</span>
                <h1 class="page-title">{{ $isAr ? 'تتبّع حالة الحجز بسهولة' : 'Track the booking status easily' }}</h1>
                <p class="page-copy">{{ $isAr ? 'أدخل رقم الحجز ورقم الهاتف لعرض تفاصيل الموعد والحالة الحالية بشكل فوري.' : 'Enter the booking code and phone number to view the appointment details and current status instantly.' }}</p>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="surface-card p-4">
            @if($errors->has('tracking'))
                <div class="alert alert-danger">{{ $errors->first('tracking') }}</div>
            @endif

            <form method="POST" action="{{ route('front.appointments.tracking.search', app()->getLocale()) }}" class="row g-3">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">{{ $isAr ? 'رقم الحجز' : 'Booking Code' }}</label>
                    <input class="form-control" name="booking_code" value="{{ old('booking_code', $bookingCode ?? '') }}" placeholder="APT-000123" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">{{ $isAr ? 'رقم الهاتف' : 'Phone Number' }}</label>
                    <input class="form-control" name="patient_phone" value="{{ old('patient_phone', $phone ?? '') }}" required>
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button class="btn btn-primary">{{ $isAr ? 'بحث' : 'Track' }}</button>
                </div>
            </form>
        </div>
    </section>

    @if(!empty($appointment))
        @php($code = 'APT-' . str_pad((string) $appointment->id, 6, '0', STR_PAD_LEFT))
        <section class="page-shell">
            <div class="surface-card p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h2 class="surface-section-title mb-0">{{ $isAr ? 'نتيجة التتبع' : 'Tracking result' }}</h2>
                    <span class="meta-pill">{{ $code }}</span>
                </div>
                <div class="info-grid-2">
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'اسم المريض' : 'Patient Name' }}</strong><span>{{ $appointment->patient_name }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'رقم الهاتف' : 'Phone' }}</strong><span>{{ $appointment->patient_phone }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'الحالة' : 'Status' }}</strong><span>{{ $appointment->status }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'نوع الحجز' : 'Booking Type' }}</strong><span>{{ $appointment->booking_type === 'vip' ? 'VIP' : ($isAr ? 'عادي' : 'Regular') }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'السعر' : 'Price' }}</strong><span>{{ number_format((float) $appointment->price, 2) }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'الفرع' : 'Branch' }}</strong><span>{{ $appointment->branch?->name ?? '-' }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'الخدمة' : 'Service' }}</strong><span>{{ $appointment->service?->title ?? '-' }}</span></div>
                    <div class="surface-card-soft p-3"><strong class="d-block mb-2">{{ $isAr ? 'الموعد' : 'Date & Time' }}</strong><span>{{ $appointment->preferred_date }} - {{ $appointment->preferred_time }}</span></div>
                </div>
                <div class="surface-card-soft p-3 mt-3">
                    <strong class="d-block mb-2">{{ $isAr ? 'ملاحظات' : 'Notes' }}</strong>
                    <div style="white-space: pre-line;">{{ $appointment->notes ?: '-' }}</div>
                </div>
            </div>
        </section>
    @endif
</div>
@endsection
