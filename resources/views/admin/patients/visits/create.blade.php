@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $isAr ? 'إضافة زيارة للمريض' : 'Add Patient Visit' }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.patients.show', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'العودة للملف' : 'Back to patient file' }}</a>
</div>

<div class="alert alert-info">
    <strong>{{ $patient->full_name }}</strong> - {{ $patient->phone }}
</div>

<form method="POST" action="{{ route('admin.patients.visits.store', [app()->getLocale(), $patient->id]) }}" enctype="multipart/form-data">
    @csrf
    @include('admin.patients.visits._form')
    <button class="btn btn-success mt-3">{{ $isAr ? 'حفظ الزيارة' : 'Save Visit' }}</button>
</form>
@endsection
