@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $isAr ? 'تقرير الحجز' : 'Appointment Report' }} #{{ $appointment->id }}</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('admin.appointments.show', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'عودة' : 'Back' }}</a>
        <a class="btn btn-success" href="{{ route('admin.appointments.report-pdf', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'تحميل PDF' : 'Download PDF' }}</a>
        <button class="btn btn-primary" onclick="window.print()">{{ $isAr ? 'طباعة' : 'Print' }}</button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5>{{ $isAr ? 'مركز د. حليم لطب الأسنان' : 'Dr. Halim Dental Center' }}</h5>
                <div class="text-muted">{{ $isAr ? 'تقرير بيانات الحجز' : 'Appointment Information Report' }}</div>
            </div>
            <div class="text-end">
                <div class="small text-muted">{{ $isAr ? 'رقم الحجز' : 'Booking Code' }}</div>
                <div class="fw-bold fs-5">{{ $bookingCode }}</div>
            </div>
        </div>

        <div class="border rounded-3 p-3 mt-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-8 text-center">
                    {!! $barcodeSvg !!}
                    <div class="small mt-2 fw-semibold">{{ $bookingCode }}</div>
                </div>
                <div class="col-md-4 text-center">
                    <img src="{{ $qrUrl }}" alt="QR Code" class="img-fluid border rounded p-1 bg-white" style="max-width:180px;">
                    <div class="small text-muted mt-1">{{ $isAr ? 'QR لمتابعة الحجز' : 'QR for tracking' }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-md-6"><strong>{{ $isAr ? 'اسم المريض:' : 'Patient Name:' }}</strong> {{ $appointment->patient_name }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'الهاتف:' : 'Phone:' }}</strong> {{ $appointment->patient_phone }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'الفرع:' : 'Branch:' }}</strong> {{ $appointment->branch?->name ?? '-' }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'الخدمة:' : 'Service:' }}</strong> {{ $appointment->service?->title ?? '-' }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'نوع الحجز:' : 'Booking Type:' }}</strong> {{ $appointment->booking_type === 'vip' ? 'VIP' : ($isAr ? 'عادي' : 'Regular') }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'السعر:' : 'Price:' }}</strong> {{ number_format((float) $appointment->price, 2) }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'التاريخ:' : 'Date:' }}</strong> {{ $appointment->preferred_date }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'الوقت:' : 'Time:' }}</strong> {{ $appointment->preferred_time }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'الحالة:' : 'Status:' }}</strong> {{ $appointment->status }}</div>
            <div class="col-md-6"><strong>{{ $isAr ? 'المصدر:' : 'Source:' }}</strong> {{ $appointment->source ?? 'website' }}</div>
            <div class="col-12"><strong>{{ $isAr ? 'رابط المتابعة:' : 'Tracking URL:' }}</strong> <span class="small">{{ $trackingUrl }}</span></div>
            <div class="col-12"><strong>{{ $isAr ? 'ملاحظات:' : 'Notes:' }}</strong><div style="white-space: pre-line;">{{ $appointment->notes ?: '-' }}</div></div>
        </div>
    </div>
</div>
@endsection
