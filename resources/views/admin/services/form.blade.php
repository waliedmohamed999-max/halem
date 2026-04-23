@extends('layouts.admin')
@section('content')
<h4>{{ isset($service) ? 'Edit' : 'Create' }} Service</h4>
<form method="POST" enctype="multipart/form-data" action="{{ isset($service)?route('admin.services.update',[app()->getLocale(),$service]):route('admin.services.store',app()->getLocale()) }}">
@csrf @if(isset($service)) @method('PUT') @endif
<div class="row g-2">
<div class="col-md-6"><input class="form-control" name="title_ar" placeholder="title_ar" value="{{ old('title_ar',$service->title_ar ?? '') }}"></div>
<div class="col-md-6"><input class="form-control" name="title_en" placeholder="title_en" value="{{ old('title_en',$service->title_en ?? '') }}"></div>
<div class="col-md-6"><textarea class="form-control" name="description_ar" placeholder="description_ar">{{ old('description_ar',$service->description_ar ?? '') }}</textarea></div>
<div class="col-md-6"><textarea class="form-control" name="description_en" placeholder="description_en">{{ old('description_en',$service->description_en ?? '') }}</textarea></div>
<div class="col-md-6"><textarea class="form-control" name="full_content_ar" placeholder="full_content_ar">{{ old('full_content_ar',$service->full_content_ar ?? '') }}</textarea></div>
<div class="col-md-6"><textarea class="form-control" name="full_content_en" placeholder="full_content_en">{{ old('full_content_en',$service->full_content_en ?? '') }}</textarea></div>
<div class="col-md-4"><input class="form-control" name="slug" placeholder="slug" value="{{ old('slug',$service->slug ?? '') }}"></div>
<div class="col-md-4"><input class="form-control" name="seo_title" placeholder="seo_title" value="{{ old('seo_title',$service->seo_title ?? '') }}"></div>
<div class="col-md-4"><input type="number" class="form-control" name="sort_order" value="{{ old('sort_order',$service->sort_order ?? 0) }}"></div>
<div class="col-md-8"><textarea class="form-control" name="meta_description" placeholder="meta_description">{{ old('meta_description',$service->meta_description ?? '') }}</textarea></div>
<div class="col-md-4"><input type="file" class="form-control" name="image"></div>
<div class="col-md-2"><label><input type="checkbox" name="is_active" value="1" {{ old('is_active',$service->is_active ?? true) ? 'checked' : '' }}> active</label></div>
<div class="col-md-2"><label><input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$service->is_featured ?? false) ? 'checked' : '' }}> featured</label></div>
</div>
<button class="btn btn-success mt-3">Save</button>
</form>
@endsection


