@extends('layouts.admin')
@section('content')
<h4>{{ isset($branch) ? 'Edit' : 'Create' }} Branch</h4>
<form method="POST" action="{{ isset($branch)?route('admin.branches.update',[app()->getLocale(),$branch]):route('admin.branches.store',app()->getLocale()) }}">
@csrf @if(isset($branch)) @method('PUT') @endif
<div class="row g-2">
<div class="col-md-6"><input class="form-control" name="name_ar" placeholder="name_ar" value="{{ old('name_ar',$branch->name_ar ?? '') }}"></div>
<div class="col-md-6"><input class="form-control" name="name_en" placeholder="name_en" value="{{ old('name_en',$branch->name_en ?? '') }}"></div>
<div class="col-md-6"><textarea class="form-control" name="address_ar" placeholder="address_ar">{{ old('address_ar',$branch->address_ar ?? '') }}</textarea></div>
<div class="col-md-6"><textarea class="form-control" name="address_en" placeholder="address_en">{{ old('address_en',$branch->address_en ?? '') }}</textarea></div>
<div class="col-md-4"><input class="form-control" name="google_maps_url" placeholder="Google Maps URL" value="{{ old('google_maps_url',$branch->google_maps_url ?? '') }}"></div>
<div class="col-md-4"><input class="form-control" name="phone" placeholder="phone" value="{{ old('phone',$branch->phone ?? '') }}"></div>
<div class="col-md-4"><input type="number" class="form-control" name="sort_order" placeholder="sort_order" value="{{ old('sort_order',$branch->sort_order ?? 0) }}"></div>
<div class="col-md-12"><textarea class="form-control" name="working_hours" placeholder="working_hours">{{ old('working_hours',$branch->working_hours ?? '') }}</textarea></div>
<div class="col-md-2"><label><input type="checkbox" name="is_active" value="1" {{ old('is_active',$branch->is_active ?? true) ? 'checked' : '' }}> active</label></div>
</div>
<button class="btn btn-success mt-3">Save</button>
</form>
@endsection


