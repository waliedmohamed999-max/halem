@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $fullContent = $isAr ? ($service->full_content_ar ?: $service->description_ar) : ($service->full_content_en ?: $service->description_en);
@endphp

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-stars"></i> {{ $isAr ? 'تفاصيل الخدمة' : 'Service details' }}</span>
                <h1 class="page-title">{{ $service->title }}</h1>
                <p class="page-copy">{{ $service->description }}</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.services.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'العودة للخدمات' : 'Back to services' }}</a>
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary px-4">{{ $isAr ? 'احجز لهذه الخدمة' : 'Book this service' }}</a>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="split-layout">
            <article class="surface-card p-4">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'شرح الخدمة' : 'Service Overview' }}</h2>
                <div class="lh-lg" style="color:#35526f;">{!! nl2br(e((string) $fullContent)) !!}</div>
            </article>

            <aside class="front-page">
                <div class="surface-card p-4">
                    <h3 class="surface-section-title mb-3">{{ $isAr ? 'ما الذي تحصل عليه؟' : 'What to expect' }}</h3>
                    <div class="front-page">
                        <div class="surface-card-soft p-3"><span class="meta-pill"><i class="bi bi-shield-check"></i> {{ $isAr ? 'رعاية آمنة' : 'Safe care' }}</span></div>
                        <div class="surface-card-soft p-3"><span class="meta-pill"><i class="bi bi-clipboard2-pulse"></i> {{ $isAr ? 'تقييم أوضح للحالة' : 'Clearer case review' }}</span></div>
                        <div class="surface-card-soft p-3"><span class="meta-pill"><i class="bi bi-calendar2-check"></i> {{ $isAr ? 'حجز سريع ومباشر' : 'Fast booking' }}</span></div>
                    </div>
                </div>
                <div class="surface-card p-4">
                    <h3 class="surface-section-title mb-3">{{ $isAr ? 'إجراءات سريعة' : 'Quick actions' }}</h3>
                    <div class="d-grid gap-2">
                        <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز الآن' : 'Book now' }}</a>
                        <a href="{{ route('front.contact.index', app()->getLocale()) }}" class="btn btn-outline-primary">{{ $isAr ? 'استفسر الآن' : 'Ask now' }}</a>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</div>
@endsection
