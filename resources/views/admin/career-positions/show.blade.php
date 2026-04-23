@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>{{ $careerPosition->title_en }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.career-positions.index', app()->getLocale()) }}">Back</a>
</div>
<div class="row g-3">
    <div class="col-md-6"><div class="p-3 border rounded"><strong>AR title:</strong> {{ $careerPosition->title_ar }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>EN title:</strong> {{ $careerPosition->title_en }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Department:</strong> {{ $careerPosition->department_en }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Location:</strong> {{ $careerPosition->location_en }}</div></div>
    <div class="col-md-12"><div class="p-3 border rounded"><strong>Summary EN:</strong><br>{{ $careerPosition->summary_en }}</div></div>
    <div class="col-md-12"><div class="p-3 border rounded"><strong>Description EN:</strong><br>{!! nl2br(e($careerPosition->description_en)) !!}</div></div>
    <div class="col-md-12"><div class="p-3 border rounded"><strong>Requirements EN:</strong><br>{!! nl2br(e($careerPosition->requirements_en)) !!}</div></div>
</div>
@endsection
