@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')
@php($financeEntry = $appointment->financeEntries->first())
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $isAr ? 'عرض الحجز' : 'Appointment Details' }} #{{ $appointment->id }}</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('admin.appointments.index', app()->getLocale()) }}">{{ $isAr ? 'عودة' : 'Back' }}</a>
        <a class="btn btn-info" target="_blank" href="{{ route('admin.appointments.report', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'تقرير' : 'Report' }}</a>
        <a class="btn btn-warning" href="{{ route('admin.appointments.edit', [app()->getLocale(), $appointment->id]) }}">{{ $isAr ? 'تعديل' : 'Edit' }}</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'اسم المريض' : 'Patient name' }}</strong><div>{{ $appointment->patient_name }}</div></div></div>
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'رقم الهاتف' : 'Phone' }}</strong><div>{{ $appointment->patient_phone }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'الفرع' : 'Branch' }}</strong><div>{{ $appointment->branch?->name ?? '-' }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'الخدمة' : 'Service' }}</strong><div>{{ $appointment->service?->title ?? '-' }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'الحالة' : 'Status' }}</strong><div>{{ $appointment->status }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'نوع الحجز' : 'Booking Type' }}</strong><div>{{ $appointment->booking_type === 'vip' ? 'VIP' : ($isAr ? 'عادي' : 'Regular') }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'السعر' : 'Price' }}</strong><div>{{ number_format((float) $appointment->price, 2) }}</div></div></div>
    <div class="col-md-4"><div class="card card-body"><strong>{{ $isAr ? 'المصدر' : 'Source' }}</strong><div>{{ $appointment->source ?? 'website' }}</div></div></div>
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'التاريخ' : 'Preferred date' }}</strong><div>{{ $appointment->preferred_date }}</div></div></div>
    <div class="col-md-6"><div class="card card-body"><strong>{{ $isAr ? 'الوقت' : 'Preferred time' }}</strong><div>{{ $appointment->preferred_time }}</div></div></div>
    <div class="col-12"><div class="card card-body"><strong>{{ $isAr ? 'ملاحظات' : 'Notes' }}</strong><div>{{ $appointment->notes ?: '-' }}</div></div></div>
</div>

<div class="row g-3 mt-1">
    <div class="col-md-6">
        <div class="card card-body">
            <strong>{{ $isAr ? 'القيد المالي المرتبط' : 'Linked Finance Entry' }}</strong>
            @if($financeEntry)
                <div class="mt-2">
                    <div>{{ $financeEntry->title }}</div>
                    <small class="text-muted">{{ $financeEntry->entry_kind }} / {{ $financeEntry->record_status }}</small>
                    <div class="fw-semibold mt-1">{{ number_format((float) $financeEntry->amount, 2) }}</div>
                </div>
                @can('manage-finance')
                    <a class="btn btn-sm btn-outline-primary mt-2" href="{{ route('admin.finance.show', [app()->getLocale(), $financeEntry->id]) }}">
                        {{ $isAr ? 'فتح القيد المالي' : 'Open Finance Entry' }}
                    </a>
                @endcan
            @else
                <div class="text-muted mt-2">{{ $isAr ? 'لا يوجد قيد مالي مرتبط' : 'No linked finance entry' }}</div>
            @endif
        </div>
    </div>
</div>

<div class="mt-3">
    @if($appointment->visit)
        <a class="btn btn-outline-success" href="{{ route('admin.patients.show', [app()->getLocale(), $appointment->visit->patient_id]) }}">
            {{ $isAr ? 'تم التحويل - فتح ملف المريض' : 'Already Converted - Open Patient File' }}
        </a>
    @else
        <form method="POST" action="{{ route('admin.appointments.convert-to-visit', [app()->getLocale(), $appointment->id]) }}">
            @csrf
            <button class="btn btn-success">{{ $isAr ? 'تحويل هذا الحجز إلى زيارة' : 'Convert This Appointment to Visit' }}</button>
        </form>
    @endif
</div>
@endsection
