@extends('layouts.admin')

@section('content')
@php($isAr = app()->getLocale() === 'ar')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ $isAr ? 'تعديل قيد مالي' : 'Edit Finance Entry' }} #{{ $finance->id }}</h4>
</div>
@include('admin.finance.form', ['finance' => $finance])
@endsection

