@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $title = $isAr ? $page->title_ar : ($page->title_en ?: $page->title_ar);
    $content = $isAr ? $page->content_ar : ($page->content_en ?: $page->content_ar);
    $isAbout = $page->slug === 'about';
    $aboutServiceIcons = [
        'bi-stars',
        'bi-shield-check',
        'bi-heart-pulse',
        'bi-emoji-smile',
        'bi-gem',
        'bi-brightness-high',
        'bi-droplet-half',
        'bi-bandaid',
    ];

    $resolveMediaUrl = static function (?string $path): ?string {
        if (! $path) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        if (\Illuminate\Support\Str::startsWith($path, ['/storage/', 'storage/'])) {
            return asset(ltrim($path, '/'));
        }
        return asset('storage/' . ltrim($path, '/'));
    };
@endphp

<style>
    .page-hero {
        border: 1px solid #d9e5f2;
        border-radius: 1.1rem;
        background: linear-gradient(120deg, #f7fbff 0%, #ebf4ff 100%);
        padding: 1.6rem;
        margin-bottom: 1rem;
    }
    .page-hero h1 {
        margin-bottom: .55rem;
        font-weight: 800;
    }
    .page-sub {
        color: #59728f;
        margin-bottom: 0;
    }
    .article-box {
        border: 1px solid #dce7f2;
        border-radius: 1rem;
        background: #fff;
        padding: 1.2rem;
        box-shadow: 0 10px 24px rgba(22, 56, 100, .08);
    }
    .article-box p,
    .article-box li {
        color: #294763;
        line-height: 1.9;
    }
    .article-box h2,
    .article-box h3 {
        color: #12365f;
        margin-top: 1rem;
        margin-bottom: .6rem;
        font-weight: 800;
    }
    .soft-card {
        border: 1px solid #dce8f4;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 22px rgba(20, 55, 99, .07);
    }
    .stat-card {
        padding: 1rem;
        text-align: center;
        height: 100%;
    }
    .stat-num {
        font-size: 1.65rem;
        font-weight: 900;
        color: #17487e;
        line-height: 1;
    }
    .stat-label {
        color: #5f7691;
        font-size: .9rem;
    }
    .about-service-card {
        border: 1px solid #d7e6f4;
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f5faff 100%);
        padding: 0;
        height: 100%;
        overflow: hidden;
        box-shadow: 0 10px 24px rgba(22, 56, 100, .08);
        transition: transform .22s ease, box-shadow .22s ease;
        position: relative;
    }
    .about-service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 30px rgba(22, 56, 100, .14);
    }
    .about-service-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .7rem;
        padding: .85rem .9rem .45rem;
    }
    .about-service-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #1f4d88;
        background: linear-gradient(135deg, #d5e9ff, #eff7ff);
        border: 1px solid #c8def5;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .6);
        animation: servicePulse 2.6s ease-in-out infinite;
    }
    @keyframes servicePulse {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .about-service-tag {
        font-size: .74rem;
        font-weight: 700;
        color: #1f5e9a;
        background: #eaf4ff;
        border: 1px solid #d2e6f9;
        border-radius: 999px;
        padding: .18rem .5rem;
    }
    .about-service-body {
        padding: 0 .9rem .95rem;
    }
    .about-service-card h6 {
        font-size: 1.07rem;
        color: #143b66;
        margin-bottom: .45rem;
        font-weight: 800;
    }
    .about-service-card p {
        margin-bottom: .65rem;
        color: #5b7490;
        line-height: 1.75;
    }
    .about-service-link {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        color: #1767c6;
        font-weight: 700;
        text-decoration: none;
    }
    .about-service-link:hover {
        color: #0f4f9d;
    }
    .about-service-link i {
        transition: transform .2s ease;
    }
    .about-service-card:hover .about-service-link i {
        transform: translateX({{ $isAr ? '-3px' : '3px' }});
    }
    .about-doctor {
        border: 1px solid #d8e5f2;
        border-radius: .9rem;
        background: #fff;
        overflow: hidden;
        height: 100%;
    }
    .about-doctor img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        background: #eaf2fb;
    }
    .about-doctor-body {
        padding: .85rem;
    }
    .hours-row {
        border-bottom: 1px dashed #d8e3ef;
        padding: .45rem 0;
    }
    .hours-row:last-child {
        border-bottom: none;
    }
    .about-cta {
        border: 1px solid #cde0f2;
        border-radius: 1rem;
        background: linear-gradient(120deg, #0f3f73 0%, #1f5e9a 100%);
        color: #fff;
        padding: 1.4rem;
    }
    .about-cta p {
        opacity: .95;
        margin-bottom: 0;
    }
</style>

@if($isAbout && !empty($aboutData))
    <section class="page-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1>{{ $title }}</h1>
                <p class="page-sub">{{ $isAr ? 'رعاية متكاملة لصحة الفم والأسنان بأحدث التقنيات وعلى يد أطباء متخصصين.' : 'Comprehensive oral and dental care with modern technology and experienced doctors.' }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز الآن' : 'Book now' }}</a>
                <a href="{{ route('front.services.index', app()->getLocale()) }}" class="btn btn-outline-primary">{{ $isAr ? 'تصفح الخدمات' : 'Browse services' }}</a>
            </div>
        </div>
    </section>

    <section class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="soft-card stat-card">
                <div class="stat-num">{{ $aboutData['stats']['doctors'] }}+</div>
                <div class="stat-label">{{ $isAr ? 'طبيب متخصص' : 'Specialist doctors' }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="soft-card stat-card">
                <div class="stat-num">{{ $aboutData['stats']['services'] }}+</div>
                <div class="stat-label">{{ $isAr ? 'خدمة علاجية وتجميلية' : 'Dental services' }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="soft-card stat-card">
                <div class="stat-num">{{ number_format($aboutData['stats']['patients']) }}+</div>
                <div class="stat-label">{{ $isAr ? 'مريض راضٍ' : 'Happy patients' }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="soft-card stat-card">
                <div class="stat-num">{{ $aboutData['stats']['experience_years'] }}+</div>
                <div class="stat-label">{{ $isAr ? 'سنة خبرة' : 'Years of experience' }}</div>
            </div>
        </div>
    </section>

    <section class="row g-3 mb-3">
        <div class="col-lg-8">
            <article class="article-box">{!! $content !!}</article>
        </div>
        <div class="col-lg-4">
            <div class="soft-card p-3 h-100">
                <h5 class="mb-3">{{ $isAr ? 'لماذا يختارنا المرضى؟' : 'Why patients choose us?' }}</h5>
                <ul class="mb-0">
                    <li>{{ $isAr ? 'تعقيم دقيق وفق معايير مكافحة العدوى.' : 'Strict sterilization and infection control standards.' }}</li>
                    <li>{{ $isAr ? 'أجهزة تشخيص حديثة وخطط علاج واضحة.' : 'Modern diagnostics and clear treatment plans.' }}</li>
                    <li>{{ $isAr ? 'متابعة بعد العلاج لضمان أفضل نتيجة.' : 'Post-treatment follow-up for long-term outcomes.' }}</li>
                    <li>{{ $isAr ? 'خيارات علاج تناسب احتياج كل مريض.' : 'Personalized treatment options for every patient.' }}</li>
                </ul>
            </div>
        </div>
    </section>

    <section class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 class="mb-0">{{ $isAr ? 'خدماتنا الأساسية' : 'Core Services' }}</h4>
            <a href="{{ route('front.services.index', app()->getLocale()) }}" class="btn btn-sm btn-outline-primary">{{ $isAr ? 'عرض الكل' : 'View all' }}</a>
        </div>
        <div class="row g-3">
            @foreach($aboutData['featuredServices'] as $index => $service)
                <div class="col-md-6 col-lg-4">
                    <div class="about-service-card">
                        @php
                            $icon = $aboutServiceIcons[$index % count($aboutServiceIcons)];
                        @endphp
                        <div class="about-service-head">
                            <span class="about-service-tag">{{ $isAr ? 'خدمة أساسية' : 'Core Service' }}</span>
                            <span class="about-service-icon"><i class="bi {{ $icon }}"></i></span>
                        </div>
                        <div class="about-service-body">
                            <h6>{{ $service->title }}</h6>
                            <p>{{ \Illuminate\Support\Str::limit($service->description, 108) }}</p>
                            <a class="about-service-link" href="{{ route('front.services.show', [app()->getLocale(), $service->slug]) }}">
                                {{ $isAr ? 'تفاصيل الخدمة' : 'Service details' }}
                                <i class="bi {{ $isAr ? 'bi-arrow-left-short' : 'bi-arrow-right-short' }}"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h4 class="mb-0">{{ $isAr ? 'فريقنا الطبي' : 'Our Team' }}</h4>
                <a href="{{ route('front.doctors.index', app()->getLocale()) }}" class="btn btn-sm btn-outline-primary">{{ $isAr ? 'كل الأطباء' : 'All doctors' }}</a>
            </div>
            <div class="row g-3">
                @foreach($aboutData['featuredDoctors'] as $doctor)
                    @php
                        $doctorPhoto = $resolveMediaUrl($doctor->photo) ?: 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80';
                    @endphp
                    <div class="col-sm-6">
                        <article class="about-doctor">
                            <img src="{{ $doctorPhoto }}" alt="{{ $doctor->name }}">
                            <div class="about-doctor-body">
                                <h6 class="mb-1">{{ $doctor->name }}</h6>
                                <p class="mb-1 text-secondary">{{ $doctor->specialty }}</p>
                                <small class="text-muted">{{ $doctor->years_experience }} {{ $isAr ? 'سنوات خبرة' : 'Years of experience' }}</small>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="soft-card p-3 h-100">
                <h5 class="mb-3">{{ $isAr ? 'ساعات العمل' : 'Working Hours' }}</h5>
                @forelse($aboutData['workingHours'] as $hour)
                    <div class="d-flex justify-content-between hours-row">
                        <span>{{ $isAr ? $hour->day_label_ar : $hour->day_label_en }}</span>
                        @if($hour->is_open)
                            <span>{{ substr($hour->open_at, 0, 5) }} - {{ substr($hour->close_at, 0, 5) }}</span>
                        @else
                            <span class="text-danger">{{ $isAr ? 'مغلق' : 'Closed' }}</span>
                        @endif
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ $isAr ? 'لا توجد بيانات ساعات عمل حالياً.' : 'No working hour data yet.' }}</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="about-cta d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="mb-1">{{ $isAr ? 'جاهز تبدأ خطة علاجك؟' : 'Ready to start your treatment plan?' }}</h4>
            <p>{{ $isAr ? 'احجز استشارة أولية الآن وسيقوم فريقنا بالتواصل لتأكيد الموعد.' : 'Book an initial consultation and our team will contact you to confirm your appointment.' }}</p>
        </div>
        <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-light text-primary fw-semibold">{{ $isAr ? 'احجز موعدك الآن' : 'Book your appointment' }}</a>
    </section>
@else
    <section class="page-hero">
        <h1>{{ $title }}</h1>
    </section>
    <article class="article-box">{!! $content !!}</article>
@endif
@endsection

