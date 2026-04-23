@extends('layouts.admin')
@section('content')
<h4>{{ isset($doctor)?'Edit':'Create' }} Doctor</h4>
<form method="POST" enctype="multipart/form-data" action="{{ isset($doctor)?route('admin.doctors.update',[app()->getLocale(),$doctor]):route('admin.doctors.store',app()->getLocale()) }}">@csrf @if(isset($doctor)) @method('PUT') @endif
<div class="row g-2">
<div class="col-md-4"><input class="form-control" name="name_ar" value="{{ old('name_ar',$doctor->name_ar ?? '') }}" placeholder="name_ar"></div>
<div class="col-md-4"><input class="form-control" name="name_en" value="{{ old('name_en',$doctor->name_en ?? '') }}" placeholder="name_en"></div>
<div class="col-md-4"><input type="number" class="form-control" name="years_experience" value="{{ old('years_experience',$doctor->years_experience ?? 0) }}"></div>
<div class="col-md-6"><input class="form-control" name="specialty_ar" value="{{ old('specialty_ar',$doctor->specialty_ar ?? '') }}" placeholder="specialty_ar"></div>
<div class="col-md-6"><input class="form-control" name="specialty_en" value="{{ old('specialty_en',$doctor->specialty_en ?? '') }}" placeholder="specialty_en"></div>
<div class="col-md-6"><textarea class="form-control" name="bio_ar">{{ old('bio_ar',$doctor->bio_ar ?? '') }}</textarea></div>
<div class="col-md-6"><textarea class="form-control" name="bio_en">{{ old('bio_en',$doctor->bio_en ?? '') }}</textarea></div>
<div class="col-md-6">
    <label class="form-label small text-muted">expertise_ar (one item per line)</label>
    <textarea class="form-control" name="expertise_ar" rows="4">{{ old('expertise_ar',$doctor->expertise_ar ?? '') }}</textarea>
</div>
<div class="col-md-6">
    <label class="form-label small text-muted">expertise_en (one item per line)</label>
    <textarea class="form-control" name="expertise_en" rows="4">{{ old('expertise_en',$doctor->expertise_en ?? '') }}</textarea>
</div>
<div class="col-md-6">
    <label class="form-label small text-muted">booking_method_ar (one step per line)</label>
    <textarea class="form-control" name="booking_method_ar" rows="4">{{ old('booking_method_ar',$doctor->booking_method_ar ?? '') }}</textarea>
</div>
<div class="col-md-6">
    <label class="form-label small text-muted">booking_method_en (one step per line)</label>
    <textarea class="form-control" name="booking_method_en" rows="4">{{ old('booking_method_en',$doctor->booking_method_en ?? '') }}</textarea>
</div>
<div class="col-md-4"><select class="form-select" name="branch_id"><option value="">Main branch</option>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(old('branch_id',$doctor->branch_id ?? '')==$b->id)>{{ $b->name_en }}</option>@endforeach</select></div>
<div class="col-md-4"><select class="form-select" name="branch_ids[]" multiple>@foreach($branches as $b)<option value="{{ $b->id }}" @selected(in_array($b->id, old('branch_ids', isset($doctor)?$doctor->branches->pluck('id')->toArray():[])))>{{ $b->name_en }}</option>@endforeach</select></div>
<div class="col-md-4">
    <input type="file" class="form-control" name="photo">
    @if(!empty($doctor?->photo))
        @php
            $photo = $doctor->photo;
            if (!\Illuminate\Support\Str::startsWith($photo, ['http://', 'https://', '/storage/', 'storage/'])) {
                $photo = 'storage/' . ltrim($photo, '/');
            }
            if (\Illuminate\Support\Str::startsWith($photo, '/storage/')) {
                $photo = ltrim($photo, '/');
            }
        @endphp
        <img src="{{ \Illuminate\Support\Str::startsWith($photo, ['http://', 'https://']) ? $photo : asset($photo) }}" alt="doctor photo" class="img-thumbnail mt-2" style="max-height:120px;">
    @endif
</div>
<div class="col-md-2"><input type="number" class="form-control" name="sort_order" value="{{ old('sort_order',$doctor->sort_order ?? 0) }}"></div>
<div class="col-md-2"><label><input type="checkbox" name="is_active" value="1" {{ old('is_active',$doctor->is_active ?? true) ? 'checked' : '' }}> active</label></div>
<div class="col-md-2"><label><input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$doctor->is_featured ?? false) ? 'checked' : '' }}> featured</label></div>
</div><button class="btn btn-success mt-3">Save</button></form>
@endsection


