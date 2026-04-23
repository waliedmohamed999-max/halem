@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';

    $promoItems = old('promo.items', $promo->payload['items'] ?? []);
    $offerItems = old('offers.items', $offers->payload['items'] ?? []);
    $advantageItems = old('advantages.items', $advantages->payload['items'] ?? []);
    $serviceIconsMap = old('service_icons.icons', $serviceHighlights->payload['icons'] ?? []);
@endphp

<style>
    .marketing-nav {
        border: 1px solid #d9e2ec;
        border-radius: .9rem;
        padding: .75rem;
        background: #f8fbff;
        margin-bottom: 1rem;
    }
    .marketing-nav a {
        text-decoration: none;
        border: 1px solid #c7d9ef;
        border-radius: 999px;
        padding: .35rem .85rem;
        color: #1f4d8b;
        font-weight: 600;
        display: inline-block;
        margin: .25rem;
    }
    .marketing-panel {
        border: 1px solid #d9e2ec;
        border-radius: 1rem;
        background: #fff;
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .marketing-panel-head {
        background: linear-gradient(120deg, #0f3466, #1f5b9e);
        color: #fff;
        padding: .9rem 1rem;
        font-weight: 700;
    }
    .marketing-panel-body {
        padding: 1rem;
    }
    .item-box {
        border: 1px dashed #bfd2e8;
        border-radius: .8rem;
        padding: .9rem;
        margin-bottom: .75rem;
        background: #f8fbff;
    }
    .item-title {
        font-weight: 700;
        color: #1f4d8b;
        margin-bottom: .65rem;
    }
    .img-preview {
        width: 100%;
        max-height: 120px;
        object-fit: cover;
        border: 1px solid #d1dfed;
        border-radius: .55rem;
        background: #fff;
    }
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h4 class="mb-0">{{ $isAr ? 'إدارة البنرات والعروض والمميزات' : 'Manage Banners, Offers & Advantages' }}</h4>
    <a href="{{ route('admin.home-sections.index', app()->getLocale()) }}" class="btn btn-outline-secondary btn-sm">
        {{ $isAr ? 'العودة لأقسام الرئيسية' : 'Back to Home Sections' }}
    </a>
</div>

<div class="marketing-nav">
    <a href="#promo-panel">{{ $isAr ? 'البنرات المتحركة' : 'Promo Banners' }}</a>
    <a href="#offers-panel">{{ $isAr ? 'العروض' : 'Offers' }}</a>
    <a href="#advantages-panel">{{ $isAr ? 'المميزات' : 'Advantages' }}</a>
    <a href="#service-icons-panel">{{ $isAr ? 'أيقونات الخدمات' : 'Service Icons' }}</a>
</div>

<form method="POST" action="{{ route('admin.marketing-sections.update', app()->getLocale()) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <section id="promo-panel" class="marketing-panel">
        <div class="marketing-panel-head">01. {{ $isAr ? 'قائمة البنرات المتحركة' : 'Promo Banner List' }}</div>
        <div class="marketing-panel-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم AR' : 'Section Title AR' }}</label>
                    <input class="form-control" name="promo[title_ar]" value="{{ old('promo.title_ar', $promo->title_ar) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم EN' : 'Section Title EN' }}</label>
                    <input class="form-control" name="promo[title_en]" value="{{ old('promo.title_en', $promo->title_en) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ $isAr ? 'الترتيب' : 'Sort' }}</label>
                    <input type="number" class="form-control" name="promo[sort_order]" value="{{ old('promo.sort_order', $promo->sort_order) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="promo_active" name="promo[is_active]" value="1" {{ old('promo.is_active', $promo->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="promo_active">{{ $isAr ? 'مفعل' : 'Active' }}</label>
                    </div>
                </div>
            </div>

            @for($i = 0; $i < 3; $i++)
                @php $item = $promoItems[$i] ?? []; @endphp
                <div class="item-box">
                    <div class="item-title">{{ $isAr ? 'بنر رقم' : 'Banner #' }} {{ $i + 1 }}</div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">{{ $isAr ? 'شارة AR' : 'Badge AR' }}</label>
                            <input class="form-control" name="promo[items][{{ $i }}][badge_ar]" value="{{ $item['badge_ar'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ $isAr ? 'شارة EN' : 'Badge EN' }}</label>
                            <input class="form-control" name="promo[items][{{ $i }}][badge_en]" value="{{ $item['badge_en'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ $isAr ? 'عنوان AR' : 'Title AR' }}</label>
                            <input class="form-control" name="promo[items][{{ $i }}][title_ar]" value="{{ $item['title_ar'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ $isAr ? 'عنوان EN' : 'Title EN' }}</label>
                            <input class="form-control" name="promo[items][{{ $i }}][title_en]" value="{{ $item['title_en'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'نص AR' : 'Subtitle AR' }}</label>
                            <textarea class="form-control" rows="2" name="promo[items][{{ $i }}][subtitle_ar]">{{ $item['subtitle_ar'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'نص EN' : 'Subtitle EN' }}</label>
                            <textarea class="form-control" rows="2" name="promo[items][{{ $i }}][subtitle_en]">{{ $item['subtitle_en'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'الهاتف' : 'Phone' }}</label>
                            <input class="form-control" name="promo[items][{{ $i }}][phone]" value="{{ $item['phone'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'رابط الصورة (اختياري)' : 'Image URL (Optional)' }}</label>
                            <input class="form-control" name="promo[items][{{ $i }}][bg_image]" value="{{ $item['bg_image'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'أو رفع صورة' : 'Or Upload Image' }}</label>
                            <input class="form-control" type="file" name="promo[item_images][{{ $i }}]" accept=".jpg,.jpeg,.png,.webp,image/*">
                        </div>
                        <div class="col-md-4">
                            @if(!empty($item['bg_image']))
                                <img class="img-preview" src="{{ $item['bg_image'] }}" alt="promo image">
                            @endif
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <section id="offers-panel" class="marketing-panel">
        <div class="marketing-panel-head">02. {{ $isAr ? 'قائمة العروض' : 'Offers List' }}</div>
        <div class="marketing-panel-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم AR' : 'Section Title AR' }}</label>
                    <input class="form-control" name="offers[title_ar]" value="{{ old('offers.title_ar', $offers->title_ar) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم EN' : 'Section Title EN' }}</label>
                    <input class="form-control" name="offers[title_en]" value="{{ old('offers.title_en', $offers->title_en) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ $isAr ? 'الترتيب' : 'Sort' }}</label>
                    <input type="number" class="form-control" name="offers[sort_order]" value="{{ old('offers.sort_order', $offers->sort_order) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="offers_active" name="offers[is_active]" value="1" {{ old('offers.is_active', $offers->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="offers_active">{{ $isAr ? 'مفعل' : 'Active' }}</label>
                    </div>
                </div>
            </div>

            @for($i = 0; $i < 6; $i++)
                @php $item = $offerItems[$i] ?? []; @endphp
                <div class="item-box">
                    <div class="item-title">{{ $isAr ? 'عرض رقم' : 'Offer #' }} {{ $i + 1 }}</div>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">{{ $isAr ? 'اسم AR' : 'Title AR' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][title_ar]" value="{{ $item['title_ar'] ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ $isAr ? 'اسم EN' : 'Title EN' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][title_en]" value="{{ $item['title_en'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ $isAr ? 'خصم AR' : 'Discount AR' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][discount_ar]" value="{{ $item['discount_ar'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ $isAr ? 'خصم EN' : 'Discount EN' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][discount_en]" value="{{ $item['discount_en'] ?? '' }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">{{ $isAr ? 'السعر' : 'Price' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][price]" value="{{ $item['price'] ?? '' }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">{{ $isAr ? 'العملة' : 'Cur.' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][currency]" value="{{ $item['currency'] ?? 'EGP' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'رابط الصورة (اختياري)' : 'Image URL (Optional)' }}</label>
                            <input class="form-control" name="offers[items][{{ $i }}][image]" value="{{ $item['image'] ?? '' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ $isAr ? 'أو رفع صورة' : 'Or Upload Image' }}</label>
                            <input class="form-control" type="file" name="offers[item_images][{{ $i }}]" accept=".jpg,.jpeg,.png,.webp,image/*">
                        </div>
                        <div class="col-md-4">
                            @if(!empty($item['image']))
                                <img class="img-preview" src="{{ $item['image'] }}" alt="offer image">
                            @endif
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <section id="advantages-panel" class="marketing-panel">
        <div class="marketing-panel-head">03. {{ $isAr ? 'قائمة المميزات' : 'Advantages List' }}</div>
        <div class="marketing-panel-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم AR' : 'Section Title AR' }}</label>
                    <input class="form-control" name="advantages[title_ar]" value="{{ old('advantages.title_ar', $advantages->title_ar) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم EN' : 'Section Title EN' }}</label>
                    <input class="form-control" name="advantages[title_en]" value="{{ old('advantages.title_en', $advantages->title_en) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ $isAr ? 'الترتيب' : 'Sort' }}</label>
                    <input type="number" class="form-control" name="advantages[sort_order]" value="{{ old('advantages.sort_order', $advantages->sort_order) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="advantages_active" name="advantages[is_active]" value="1" {{ old('advantages.is_active', $advantages->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="advantages_active">{{ $isAr ? 'مفعل' : 'Active' }}</label>
                    </div>
                </div>
            </div>

            @for($i = 0; $i < 6; $i++)
                @php $item = $advantageItems[$i] ?? []; @endphp
                <div class="item-box">
                    <div class="item-title">{{ $isAr ? 'ميزة رقم' : 'Advantage #' }} {{ $i + 1 }}</div>
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">{{ $isAr ? 'أيقونة Bootstrap' : 'Bootstrap Icon' }}</label>
                            <input class="form-control" name="advantages[items][{{ $i }}][icon]" value="{{ $item['icon'] ?? 'bi-patch-check' }}" placeholder="bi-award">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ $isAr ? 'عنوان AR' : 'Title AR' }}</label>
                            <input class="form-control" name="advantages[items][{{ $i }}][title_ar]" value="{{ $item['title_ar'] ?? '' }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ $isAr ? 'عنوان EN' : 'Title EN' }}</label>
                            <input class="form-control" name="advantages[items][{{ $i }}][title_en]" value="{{ $item['title_en'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'وصف AR' : 'Description AR' }}</label>
                            <textarea class="form-control" rows="3" name="advantages[items][{{ $i }}][description_ar]">{{ $item['description_ar'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ $isAr ? 'وصف EN' : 'Description EN' }}</label>
                            <textarea class="form-control" rows="3" name="advantages[items][{{ $i }}][description_en]">{{ $item['description_en'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <section id="service-icons-panel" class="marketing-panel">
        <div class="marketing-panel-head">04. {{ $isAr ? 'أيقونات الخدمات المميزة' : 'Featured Services Icons' }}</div>
        <div class="marketing-panel-body">
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم AR' : 'Section Title AR' }}</label>
                    <input class="form-control" name="service_icons[title_ar]" value="{{ old('service_icons.title_ar', $serviceHighlights->title_ar) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'عنوان القسم EN' : 'Section Title EN' }}</label>
                    <input class="form-control" name="service_icons[title_en]" value="{{ old('service_icons.title_en', $serviceHighlights->title_en) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ $isAr ? 'الترتيب' : 'Sort' }}</label>
                    <input type="number" class="form-control" name="service_icons[sort_order]" value="{{ old('service_icons.sort_order', $serviceHighlights->sort_order) }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="service_icons_active" name="service_icons[is_active]" value="1" {{ old('service_icons.is_active', $serviceHighlights->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="service_icons_active">{{ $isAr ? 'مفعل' : 'Active' }}</label>
                    </div>
                </div>
            </div>

            <div class="alert alert-light border">
                {{ $isAr ? 'استخدم كلاس Bootstrap Icons مثل: bi-stars أو bi-shield-check أو bi-heart-pulse' : 'Use Bootstrap Icon class like: bi-stars, bi-shield-check, or bi-heart-pulse' }}
            </div>

            @foreach($services as $service)
                @php
                    $serviceKey = $service->slug ?: $service->id;
                    $serviceIcon = $serviceIconsMap[$service->slug] ?? ($serviceIconsMap[$service->id] ?? 'bi-stars');
                @endphp
                <div class="item-box">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">{{ $isAr ? 'الخدمة' : 'Service' }}</label>
                            <input class="form-control" value="{{ $isAr ? $service->title_ar : $service->title_en }}" disabled>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ $isAr ? 'أيقونة الخدمة' : 'Service Icon' }}</label>
                            <input class="form-control" name="service_icons[icons][{{ $serviceKey }}]" value="{{ $serviceIcon }}" placeholder="bi-stars">
                        </div>
                        <div class="col-md-2">
                            <span class="btn btn-outline-secondary w-100 disabled">
                                <i class="bi {{ $serviceIcon }}"></i>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <button class="btn btn-success px-4">{{ $isAr ? 'حفظ كل التعديلات' : 'Save All Changes' }}</button>
</form>
@endsection