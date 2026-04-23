@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')
<h4 class="mb-3">{{ $isAr ? 'تعديل بيانات المريض' : 'Edit Patient Data' }}</h4>

<form method="POST" action="{{ route('admin.patients.update', [app()->getLocale(), $patient->id]) }}">
    @csrf
    @method('PUT')
    @include('admin.patients._form')
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-success">{{ $isAr ? 'تحديث' : 'Update' }}</button>
        <a class="btn btn-outline-secondary" href="{{ route('admin.patients.show', [app()->getLocale(), $patient->id]) }}">{{ $isAr ? 'عودة للملف' : 'Back to file' }}</a>
    </div>
</form>
@endsection

