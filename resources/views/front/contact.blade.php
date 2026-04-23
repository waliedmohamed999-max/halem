@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $sitePhone = \App\Models\Setting::getValue('site_phone', '01028234921');
    $siteEmail = \App\Models\Setting::getValue('site_email', 'info@drhalim-dental.com');
    $siteCity = \App\Models\Setting::getValue('site_city', 'مصر - القاهرة');
    $waUrl = \App\Models\Setting::getValue('whatsapp_url');
    $mapsUrl = \App\Models\Setting::getValue('google_maps_url');
@endphp

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-headset"></i> {{ $isAr ? 'تواصل مباشر' : 'Direct support' }}</span>
                <h1 class="page-title">{{ $isAr ? 'تواصل معنا بسرعة عبر نموذج واضح وبيانات مباشرة' : 'Reach out through a clearer contact form and direct channels' }}</h1>
                <p class="page-copy">{{ $isAr ? 'اكتب رسالتك، اطلب معاودة الاتصال، أو استخدم واتساب والموقع على الخريطة من نفس الصفحة.' : 'Send a message, request a callback, or use WhatsApp and map access from the same page.' }}</p>
            </div>
            <div class="page-actions">
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $sitePhone) }}" class="btn btn-primary px-4">{{ $isAr ? 'اتصال سريع' : 'Quick call' }}</a>
                @if($waUrl)
                    <a href="{{ $waUrl }}" target="_blank" class="btn btn-outline-success px-4">{{ $isAr ? 'واتساب' : 'WhatsApp' }}</a>
                @endif
            </div>
        </div>
    </section>
    <section class="page-shell">
        <div class="split-layout">
            <div class="surface-card p-4">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'نموذج التواصل' : 'Contact Form' }}</h2>
                <form method="POST" action="{{ route('front.contact.store', app()->getLocale()) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">{{ $isAr ? 'الاسم' : 'Name' }}</label><input class="form-control" name="name" value="{{ old('name') }}" required></div>
                        <div class="col-md-6"><label class="form-label">{{ $isAr ? 'رقم الهاتف' : 'Phone Number' }}</label><input class="form-control" name="phone" value="{{ old('phone') }}" required></div>
                        <div class="col-12"><label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label><input class="form-control" type="email" name="email" value="{{ old('email') }}"></div>
                        <div class="col-12"><label class="form-label">{{ $isAr ? 'رسالتك' : 'Your Message' }}</label><textarea class="form-control" rows="6" name="message" required>{{ old('message') }}</textarea></div>
                    </div>
                    <div class="submit-bar">
                        <small class="text-secondary">{{ $isAr ? 'لن تتم مشاركة بياناتك مع أي جهة خارجية.' : 'Your data will not be shared with third parties.' }}</small>
                        <button class="btn btn-primary px-4">{{ $isAr ? 'إرسال الرسالة' : 'Send Message' }}</button>
                    </div>
                </form>
            </div>
            <div class="front-page">
                <div class="surface-card p-4">
                    <h3 class="surface-section-title mb-3">{{ $isAr ? 'بيانات التواصل' : 'Contact Details' }}</h3>
                    <div class="front-page">
                        <div class="surface-card-soft p-3"><small class="text-secondary d-block mb-1">{{ $isAr ? 'الهاتف' : 'Phone' }}</small><strong>{{ $sitePhone }}</strong></div>
                        <div class="surface-card-soft p-3"><small class="text-secondary d-block mb-1">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</small><strong>{{ $siteEmail }}</strong></div>
                        <div class="surface-card-soft p-3"><small class="text-secondary d-block mb-1">{{ $isAr ? 'العنوان' : 'Location' }}</small><strong>{{ $siteCity }}</strong></div>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        @if($mapsUrl)
                            <a href="{{ $mapsUrl }}" target="_blank" class="btn btn-outline-dark">{{ $isAr ? 'فتح الموقع على الخريطة' : 'Open on map' }}</a>
                        @endif
                    </div>
                </div>
                <div class="surface-card p-4">
                    <h3 class="surface-section-title mb-3">{{ $isAr ? 'ساعات العمل' : 'Working Hours' }}</h3>
                    <div class="working-hours-list">
                        @foreach($hours as $hour)
                            <div class="work-hour-row">
                                <span>{{ $isAr ? $hour->day_label_ar : $hour->day_label_en }}</span>
                                <span>{{ $hour->is_open ? (substr((string) $hour->open_at,0,5) . ' - ' . substr((string) $hour->close_at,0,5)) : ($isAr ? 'مغلق' : 'Closed') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
