@extends('layouts.admin')
@section('content')
<h4>{{ isset($careerPosition) ? 'Edit' : 'Create' }} Career Position</h4>
<form method="POST" action="{{ isset($careerPosition) ? route('admin.career-positions.update', [app()->getLocale(), $careerPosition]) : route('admin.career-positions.store', app()->getLocale()) }}">
    @csrf
    @if(isset($careerPosition))
        @method('PUT')
    @endif
    <div class="row g-2">
        <div class="col-md-6"><input class="form-control" name="title_ar" placeholder="title_ar" value="{{ old('title_ar', $careerPosition->title_ar ?? '') }}"></div>
        <div class="col-md-6"><input class="form-control" name="title_en" placeholder="title_en" value="{{ old('title_en', $careerPosition->title_en ?? '') }}"></div>
        <div class="col-md-6"><input class="form-control" name="department_ar" placeholder="department_ar" value="{{ old('department_ar', $careerPosition->department_ar ?? '') }}"></div>
        <div class="col-md-6"><input class="form-control" name="department_en" placeholder="department_en" value="{{ old('department_en', $careerPosition->department_en ?? '') }}"></div>
        <div class="col-md-6"><input class="form-control" name="location_ar" placeholder="location_ar" value="{{ old('location_ar', $careerPosition->location_ar ?? '') }}"></div>
        <div class="col-md-6"><input class="form-control" name="location_en" placeholder="location_en" value="{{ old('location_en', $careerPosition->location_en ?? '') }}"></div>
        <div class="col-md-4">
            <select class="form-select" name="job_type">
                <option value="full_time" @selected(old('job_type', $careerPosition->job_type ?? 'full_time') === 'full_time')>full_time</option>
                <option value="part_time" @selected(old('job_type', $careerPosition->job_type ?? '') === 'part_time')>part_time</option>
                <option value="internship" @selected(old('job_type', $careerPosition->job_type ?? '') === 'internship')>internship</option>
                <option value="contract" @selected(old('job_type', $careerPosition->job_type ?? '') === 'contract')>contract</option>
            </select>
        </div>
        <div class="col-md-4"><input class="form-control" name="experience_level" placeholder="experience_level" value="{{ old('experience_level', $careerPosition->experience_level ?? '') }}"></div>
        <div class="col-md-4"><input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $careerPosition->sort_order ?? 0) }}"></div>
        <div class="col-md-6"><textarea class="form-control" rows="3" name="summary_ar" placeholder="summary_ar">{{ old('summary_ar', $careerPosition->summary_ar ?? '') }}</textarea></div>
        <div class="col-md-6"><textarea class="form-control" rows="3" name="summary_en" placeholder="summary_en">{{ old('summary_en', $careerPosition->summary_en ?? '') }}</textarea></div>
        <div class="col-md-6"><textarea class="form-control" rows="6" name="description_ar" placeholder="description_ar">{{ old('description_ar', $careerPosition->description_ar ?? '') }}</textarea></div>
        <div class="col-md-6"><textarea class="form-control" rows="6" name="description_en" placeholder="description_en">{{ old('description_en', $careerPosition->description_en ?? '') }}</textarea></div>
        <div class="col-md-6"><textarea class="form-control" rows="6" name="requirements_ar" placeholder="requirements_ar">{{ old('requirements_ar', $careerPosition->requirements_ar ?? '') }}</textarea></div>
        <div class="col-md-6"><textarea class="form-control" rows="6" name="requirements_en" placeholder="requirements_en">{{ old('requirements_en', $careerPosition->requirements_en ?? '') }}</textarea></div>
        <div class="col-md-2">
            <label>
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $careerPosition->is_active ?? true) ? 'checked' : '' }}>
                active
            </label>
        </div>
    </div>
    <button class="btn btn-success mt-3">Save</button>
</form>
@endsection
