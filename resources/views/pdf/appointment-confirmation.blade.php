<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 18px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .card { border: 1px solid #d1d5db; border-radius: 10px; padding: 16px; }
        .title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .muted { color: #6b7280; font-size: 11px; }
        .row::after { content: ""; display: block; clear: both; }
        .col-8 { float: left; width: 66.666%; }
        .col-4 { float: left; width: 33.333%; text-align: center; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; margin-top: 12px; }
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: 700; }
    </style>
</head>
<body>
<div class="card">
    <div class="title">Appointment Confirmation</div>
    <div class="muted">Dr. Halim Dental Center</div>

    <div class="box mt-12">
        <div class="row">
            <div class="col-8 text-right">
                {!! $barcodeSvg !!}
                <div class="fw-bold mt-8">{{ $bookingCode }}</div>
            </div>
            <div class="col-4">
                <img src="{{ $qrUrl }}" alt="QR" width="130" height="130">
                <div class="muted mt-8">Tracking QR</div>
            </div>
        </div>
    </div>

    <div class="box">
        <div><strong>Name:</strong> {{ $appointment->patient_name }}</div>
        <div><strong>Phone:</strong> {{ $appointment->patient_phone }}</div>
        <div><strong>Branch:</strong> {{ $appointment->branch?->name ?? '-' }}</div>
        <div><strong>Service:</strong> {{ $appointment->service?->title ?? '-' }}</div>
        <div><strong>Booking Type:</strong> {{ $appointment->booking_type === 'vip' ? 'VIP' : 'Regular' }}</div>
        <div><strong>Price:</strong> {{ number_format((float) $appointment->price, 2) }}</div>
        <div><strong>Date:</strong> {{ $appointment->preferred_date }}</div>
        <div><strong>Time:</strong> {{ $appointment->preferred_time }}</div>
        <div><strong>Tracking URL:</strong> {{ $trackingUrl }}</div>
    </div>

    <div class="box">
        <strong>Notes:</strong>
        <div style="white-space: pre-line;">{{ $appointment->notes ?: '-' }}</div>
    </div>
</div>
</body>
</html>
