@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')
<h4 class="mb-3">{{ $isAr ? 'إضافة مريض جديد' : 'Add New Patient' }}</h4>

<form method="POST" action="{{ route('admin.patients.store', app()->getLocale()) }}">
    @csrf
    @include('admin.patients._form')
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-success">{{ $isAr ? 'حفظ' : 'Save' }}</button>
        <a class="btn btn-outline-secondary" href="{{ route('admin.patients.index', app()->getLocale()) }}">{{ $isAr ? 'عودة' : 'Back' }}</a>
    </div>
</form>
@endsection

