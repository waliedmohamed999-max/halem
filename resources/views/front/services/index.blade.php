@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $fallbackAdvantages = [
        ['icon' => 'bi-award', 'title_ar' => 'الخبرة', 'title_en' => 'Experience', 'description_ar' => 'كوادر متخصصة بخبرات عملية وتدريب مستمر لتقديم نتائج دقيقة وآمنة.', 'description_en' => 'Specialized teams with practical experience and continuous training.'],
        ['icon' => 'bi-cpu', 'title_ar' => 'التقنيات', 'title_en' => 'Technology', 'description_ar' => 'أجهزة حديثة للتشخيص والعلاج ضمن تجربة أكثر دقة وراحة.', 'description_en' => 'Modern diagnostics and treatment technology for better comfort and accuracy.'],
        ['icon' => 'bi-shield-check', 'title_ar' => 'الأمان', 'title_en' => 'Safety', 'description_ar' => 'تعقيم صارم وبروتوكولات واضحة داخل كل العيادات.', 'description_en' => 'Strict sterilization and clear clinical protocols.'],
    ];
    $advantageItems = !empty($advantages) ? $advantages : $fallbackAdvantages;
    $advantagesTitle = $isAr ? (($advantagesSection ?? null)?->title_ar ?: 'لماذا يختارنا المرضى') : (($advantagesSection ?? null)?->title_en ?: 'Why Patients Choose Us');
@endphp

<style>
    .service-grid-modern { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .service-card-modern { position:relative; display:flex; flex-direction:column; gap:.85rem; padding:1.15rem; min-height:100%; overflow:hidden; }
    .service-card-modern::before { content:''; position:absolute; inset:auto -35px -40px auto; width:120px; height:120px; border-radius:50%; background:radial-gradient(circle, rgba(80,146,223,.14), transparent 68%); pointer-events:none; }
    .service-card-modern > * { position:relative; z-index:1; }
    .service-card-head { display:flex; align-items:start; justify-content:space-between; gap:.75rem; }
    .service-card-modern .meta-pill { background:#eefaf7; }
    .service-icon-modern { width:54px; height:54px; border-radius:16px; display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg, #dcebff, #ffffff); border:1px solid #d0e2f4; color:#1d4f88; font-size:1.2rem; }
    .service-card-modern h3 { margin:0; color:#112f53; font-size:1.2rem; font-weight:800; }
    .service-card-modern p { margin:0; color:#5c7490; line-height:1.85; }
    .adv-grid-modern { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .adv-card-modern { padding:1.05rem; }
    .adv-card-modern i { width:52px; height:52px; border-radius:16px; display:inline-flex; align-items:center; justify-content:center; background:#eef5fd; color:#1d4f88; font-size:1.2rem; margin-bottom:.8rem; }
    .adv-card-modern h4 { color:#113861; font-weight:800; margin-bottom:.35rem; }
    .adv-card-modern p { margin:0; color:#607994; line-height:1.8; }
    @media (max-width: 991.98px) { .service-grid-modern, .adv-grid-modern { grid-template-columns:1fr 1fr; } }
    @media (max-width: 767.98px) { .service-grid-modern, .adv-grid-modern { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-heart-pulse"></i> {{ $isAr ? 'خدمات علاجية' : 'Clinical Services' }}</span>
                <h1 class="page-title">{{ $isAr ? 'خدمات الأسنان بخطة أوضح وتجربة أكثر احترافية' : 'Dental services with a clearer plan and a more premium experience' }}</h1>
                <p class="page-copy">{{ $isAr ? 'استعرض الخدمات الأساسية والتجميلية والعلاجية داخل مسار واضح يساعد المريض على فهم العلاج وحجز الموعد المناسب بسرعة.' : 'Browse essential, cosmetic, and restorative services in a clearer experience that helps patients understand care paths and book faster.' }}</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary px-4">{{ $isAr ? 'احجز الآن' : 'Book now' }}</a>
                <a href="{{ route('front.contact.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'استفسر عن خدمة' : 'Ask about a service' }}</a>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="page-stats">
            <div class="page-stat"><strong>{{ $services->total() }}</strong><span>{{ $isAr ? 'خدمة متاحة' : 'Available services' }}</span></div>
            <div class="page-stat"><strong>+{{ count($advantageItems) }}</strong><span>{{ $isAr ? 'عناصر تميز' : 'Differentiators' }}</span></div>
            <div class="page-stat"><strong>24/7</strong><span>{{ $isAr ? 'حجز واستفسار' : 'Booking support' }}</span></div>
        </div>
    </section>

    <section class="page-shell">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h2 class="surface-section-title mb-0">{{ $isAr ? 'الخدمات المتاحة' : 'Available Services' }}</h2>
            <span class="meta-pill"><i class="bi bi-layout-text-window"></i> {{ $isAr ? 'بطاقات أوضح ومحتوى مختصر' : 'Clear cards and concise content' }}</span>
        </div>
        <div class="service-grid-modern">
            @foreach($services as $service)
                <article class="surface-card service-card-modern">
                    <div class="service-card-head">
                        <div>
                            <span class="meta-pill">{{ $isAr ? 'خدمة مميزة' : 'Featured service' }}</span>
                            <h3 class="mt-3">{{ $service->title }}</h3>
                        </div>
                        <span class="service-icon-modern"><i class="bi bi-stars"></i></span>
                    </div>
                    <p>{{ \Illuminate\Support\Str::limit($service->description, 130) }}</p>
                    <div class="d-flex gap-2 flex-wrap mt-auto">
                        <a href="{{ route('front.services.show', [app()->getLocale(), $service->slug]) }}" class="btn btn-outline-primary">{{ $isAr ? 'عرض التفاصيل' : 'View details' }}</a>
                        <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز' : 'Book' }}</a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-4">{{ $services->links() }}</div>
    </section>

    <section class="page-shell">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h2 class="surface-section-title mb-0">{{ $advantagesTitle }}</h2>
            <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="action-link">{{ $isAr ? 'ابدأ الحجز' : 'Start booking' }} <i class="bi bi-arrow-up-left"></i></a>
        </div>
        <div class="adv-grid-modern">
            @foreach($advantageItems as $item)
                <article class="surface-card adv-card-modern">
                    <i class="bi {{ $item['icon'] ?? 'bi-patch-check' }}"></i>
                    <h4>{{ $isAr ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? '') }}</h4>
                    <p>{{ $isAr ? ($item['description_ar'] ?? '') : ($item['description_en'] ?? '') }}</p>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
