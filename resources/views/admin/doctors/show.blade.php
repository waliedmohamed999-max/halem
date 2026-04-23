@extends('layouts.admin')

@section('content')
<h4 class="mb-3">{{ $doctor->name_en }}</h4>

<div class="row g-3">
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Name AR:</strong> {{ $doctor->name_ar }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Name EN:</strong> {{ $doctor->name_en }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Specialty AR:</strong> {{ $doctor->specialty_ar }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Specialty EN:</strong> {{ $doctor->specialty_en }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Years:</strong> {{ $doctor->years_experience }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Main Branch ID:</strong> {{ $doctor->branch_id ?? '-' }}</div></div>

    <div class="col-md-6"><div class="p-3 border rounded"><strong>Bio AR:</strong><br>{!! nl2br(e($doctor->bio_ar ?? '-')) !!}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Bio EN:</strong><br>{!! nl2br(e($doctor->bio_en ?? '-')) !!}</div></div>

    <div class="col-md-6"><div class="p-3 border rounded"><strong>Expertise AR:</strong><br>{!! nl2br(e($doctor->expertise_ar ?? '-')) !!}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Expertise EN:</strong><br>{!! nl2br(e($doctor->expertise_en ?? '-')) !!}</div></div>

    <div class="col-md-6"><div class="p-3 border rounded"><strong>Booking Method AR:</strong><br>{!! nl2br(e($doctor->booking_method_ar ?? '-')) !!}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Booking Method EN:</strong><br>{!! nl2br(e($doctor->booking_method_en ?? '-')) !!}</div></div>
</div>
@endsection
