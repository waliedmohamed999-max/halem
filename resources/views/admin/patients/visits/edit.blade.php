@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $isAr ? 'تعديل زيارة المريض' : 'Edit Patient Visit' }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.patients.show', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'العودة للملف' : 'Back to patient file' }}</a>
</div>

<div class="alert alert-info">
    <strong>{{ $patient->full_name }}</strong> - {{ $patient->phone }}
</div>

<form method="POST" action="{{ route('admin.patients.visits.update', [app()->getLocale(), $patient->id, $visit->id]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.patients.visits._form')
    <button class="btn btn-success mt-3">{{ $isAr ? 'تحديث الزيارة' : 'Update Visit' }}</button>
</form>
@endsection
