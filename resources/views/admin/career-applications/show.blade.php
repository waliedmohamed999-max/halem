@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Application #{{ $careerApplication->id }}</h4>
    <a class="btn btn-outline-secondary" href="{{ route('admin.career-applications.index', app()->getLocale()) }}">Back</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Name:</strong> {{ $careerApplication->full_name }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Position:</strong> {{ $careerApplication->position?->title_en ?? '-' }}</div></div>
    <div class="col-md-4"><div class="p-3 border rounded"><strong>Phone:</strong> {{ $careerApplication->phone }}</div></div>
    <div class="col-md-4"><div class="p-3 border rounded"><strong>Email:</strong> {{ $careerApplication->email ?: '-' }}</div></div>
    <div class="col-md-4"><div class="p-3 border rounded"><strong>City:</strong> {{ $careerApplication->city ?: '-' }}</div></div>
    <div class="col-md-6"><div class="p-3 border rounded"><strong>Experience:</strong> {{ $careerApplication->experience_years ?: '-' }}</div></div>
    <div class="col-md-6">
        <div class="p-3 border rounded">
            <strong>CV:</strong>
            @if($careerApplication->cv_file)
                <a href="{{ asset('storage/' . $careerApplication->cv_file) }}" target="_blank">Open file</a>
            @else
                -
            @endif
        </div>
    </div>
    <div class="col-12"><div class="p-3 border rounded"><strong>Cover Letter:</strong><br>{!! nl2br(e($careerApplication->cover_letter ?: '-')) !!}</div></div>
</div>

<form method="POST" action="{{ route('admin.career-applications.update', [app()->getLocale(), $careerApplication]) }}" class="row g-2">
    @csrf
    @method('PUT')
    <div class="col-md-3">
        <select class="form-select" name="status">
            @foreach(['new','reviewed','interview','rejected','hired'] as $status)
                <option value="{{ $status }}" @selected(old('status', $careerApplication->status) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-9">
        <textarea class="form-control" name="admin_notes" rows="2" placeholder="admin_notes">{{ old('admin_notes', $careerApplication->admin_notes) }}</textarea>
    </div>
    <div class="col-12">
        <button class="btn btn-success">Save status</button>
    </div>
</form>
@endsection
