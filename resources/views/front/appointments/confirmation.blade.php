@extends('layouts.front')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-patch-check"></i> {{ $isAr ? 'تأكيد الحجز' : 'Appointment confirmed' }}</span>
                <h1 class="page-title">{{ $isAr ? 'تم تأكيد طلب الحجز بنجاح' : 'The appointment request has been confirmed' }}</h1>
                <p class="page-copy">{{ $isAr ? 'احتفظ بهذه الصفحة أو قم بطباعتها أو تنزيل نسخة PDF لعرضها عند الحاجة.' : 'Keep this page, print it, or download the PDF version for reference when needed.' }}</p>
            </div>
            <div class="page-actions">
                <span class="meta-pill"><i class="bi bi-upc-scan"></i> {{ $bookingCode }}</span>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="surface-card p-4">
            <div class="split-layout">
                <div class="surface-card-soft p-3 text-center">
                    {!! $barcodeSvg !!}
                    <div class="small mt-2 fw-semibold">{{ $bookingCode }}</div>
                </div>
                <div class="surface-card-soft p-3 text-center">
                    <img src="{{ $qrUrl }}" alt="QR Code" class="img-fluid border rounded p-1 bg-white" style="max-width:180px;">
                    <div class="small text-muted mt-2">{{ $isAr ? 'QR لمتابعة الحجز' : 'QR for appointment tracking' }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="info-grid-2">
            <div class="surface-card p-4">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'بيانات المريض' : 'Patient details' }}</h2>
                <div class="front-page">
                    <div><strong>{{ $isAr ? 'الاسم:' : 'Name:' }}</strong> {{ $appointment->patient_name }}</div>
                    <div><strong>{{ $isAr ? 'الهاتف:' : 'Phone:' }}</strong> {{ $appointment->patient_phone }}</div>
                </div>
            </div>
            <div class="surface-card p-4">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'بيانات الموعد' : 'Appointment details' }}</h2>
                <div class="front-page">
                    <div><strong>{{ $isAr ? 'الفرع:' : 'Branch:' }}</strong> {{ $appointment->branch?->name ?? '-' }}</div>
                    <div><strong>{{ $isAr ? 'الخدمة:' : 'Service:' }}</strong> {{ $appointment->service?->title ?? '-' }}</div>
                    <div><strong>{{ $isAr ? 'نوع الحجز:' : 'Booking Type:' }}</strong> {{ $appointment->booking_type === 'vip' ? 'VIP' : ($isAr ? 'عادي' : 'Regular') }}</div>
                    <div><strong>{{ $isAr ? 'السعر:' : 'Price:' }}</strong> {{ number_format((float) $appointment->price, 2) }}</div>
                    <div><strong>{{ $isAr ? 'التاريخ:' : 'Date:' }}</strong> {{ $appointment->preferred_date }}</div>
                    <div><strong>{{ $isAr ? 'الوقت:' : 'Time:' }}</strong> {{ $appointment->preferred_time }}</div>
                </div>
            </div>
        </div>
        @if($appointment->notes)
            <div class="surface-card p-4 mt-3">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'ملاحظات' : 'Notes' }}</h2>
                <div style="white-space: pre-line;">{{ $appointment->notes }}</div>
            </div>
        @endif
    </section>

    <section class="page-shell">
        <div class="d-flex gap-2 flex-wrap justify-content-end">
            <a class="btn btn-success" href="{{ URL::signedRoute('front.appointments.confirmation.pdf', ['locale' => app()->getLocale(), 'appointment' => $appointment->id]) }}">{{ $isAr ? 'تحميل PDF' : 'Download PDF' }}</a>
            <a class="btn btn-outline-primary" href="{{ route('front.appointments.tracking', ['locale' => app()->getLocale(), 'code' => $bookingCode]) }}">{{ $isAr ? 'متابعة الحجز' : 'Open tracking' }}</a>
            <button class="btn btn-primary" onclick="window.print()">{{ $isAr ? 'طباعة التأكيد' : 'Print confirmation' }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('front.home', app()->getLocale()) }}">{{ $isAr ? 'العودة للرئيسية' : 'Back to home' }}</a>
        </div>
    </section>
</div>
@endsection
