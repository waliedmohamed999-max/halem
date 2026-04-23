@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $resolveMediaUrl = static function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (\Illuminate\Support\Str::startsWith($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (\Illuminate\Support\Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . ltrim($path, '/'));
    };

    $heroSection = $sections->firstWhere('section_key', 'hero');
    $heroPayload = $heroSection?->payload ?? [];
    $heroTitle = $isAr ? ($heroPayload['title_ar'] ?? \App\Models\Setting::getValue('site_name')) : ($heroPayload['title_en'] ?? \App\Models\Setting::getValue('site_name'));
    $heroText = $isAr ? ($heroPayload['text_ar'] ?? \App\Models\Setting::getValue('hero_subtitle_ar')) : ($heroPayload['text_en'] ?? \App\Models\Setting::getValue('hero_subtitle_en'));

    $bannerSection = $sections->firstWhere('section_key', 'promo_banners');
    $bannerPayload = $bannerSection?->payload ?? [];
    $banners = $bannerPayload['items'] ?? [
        [
            'badge_ar' => 'إعلان خاص',
            'badge_en' => 'Special Ad',
            'title_ar' => 'ابتسامة صحية تبدأ بخطة علاج دقيقة',
            'title_en' => 'A healthy smile starts with a precise plan',
            'subtitle_ar' => 'فحص رقمي شامل وخطة مناسبة لحالتك مع فريق د. حليم.',
            'subtitle_en' => 'Digital full checkup with a treatment plan tailored to your case.',
            'phone' => '01028234921',
            'bg_image' => 'https://images.unsplash.com/photo-1629909615957-be95c2f2f6f6?auto=format&fit=crop&w=1600&q=80',
        ],
        [
            'badge_ar' => 'حجز سريع',
            'badge_en' => 'Quick Booking',
            'title_ar' => 'جلسات تجميل الأسنان بأحدث التقنيات',
            'title_en' => 'Cosmetic dentistry with modern techniques',
            'subtitle_ar' => 'احجز الآن واستفد من متابعة ما بعد الجلسة.',
            'subtitle_en' => 'Book now and get complete post-treatment follow-up.',
            'phone' => '01028234921',
            'bg_image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1600&q=80',
        ],
    ];

    $offerSection = $sections->firstWhere('section_key', 'limited_offers');
    $offerPayload = $offerSection?->payload ?? [];
    $offers = $offerPayload['items'] ?? [
        [
            'title_ar' => 'تنظيف وتلميع الأسنان',
            'title_en' => 'Teeth Cleaning & Polishing',
            'discount_ar' => 'خصم 30%',
            'discount_en' => '30% Off',
            'price' => '450',
            'currency' => 'EGP',
            'image' => 'https://images.unsplash.com/photo-1588776814546-ec7e77b77d7e?auto=format&fit=crop&w=900&q=80',
        ],
        [
            'title_ar' => 'تبييض الأسنان',
            'title_en' => 'Teeth Whitening',
            'discount_ar' => 'خصم 25%',
            'discount_en' => '25% Off',
            'price' => '1200',
            'currency' => 'EGP',
            'image' => 'https://images.unsplash.com/photo-1593022356769-11f762e25ed9?auto=format&fit=crop&w=900&q=80',
        ],
        [
            'title_ar' => 'تقويم الأسنان',
            'title_en' => 'Orthodontics',
            'discount_ar' => 'استشارة مجانية',
            'discount_en' => 'Free Consultation',
            'price' => '250',
            'currency' => 'EGP',
            'image' => 'https://images.unsplash.com/photo-1609840112855-9f0fe8cc9a8b?auto=format&fit=crop&w=900&q=80',
        ],
        [
            'title_ar' => 'حشو العصب',
            'title_en' => 'Root Canal Treatment',
            'discount_ar' => 'خصم 20%',
            'discount_en' => '20% Off',
            'price' => '900',
            'currency' => 'EGP',
            'image' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=900&q=80',
        ],
    ];

    $servicesSection = $sections->firstWhere('section_key', 'services_highlights');
    $serviceIconMap = $servicesSection?->payload['icons'] ?? [];
    $defaultServiceIcons = ['bi-shield-check', 'bi-stars', 'bi-heart-pulse', 'bi-emoji-smile', 'bi-gem', 'bi-lightning-charge'];
    $advantagesSection = $sections->firstWhere('section_key', 'services_advantages');
    $advantagesPayload = $advantagesSection?->payload ?? [];
    $advantagesTitle = $isAr ? ($advantagesSection?->title_ar ?? 'ميزات متفردة') : ($advantagesSection?->title_en ?? 'Distinctive Advantages');
    $advantagesItems = $advantagesPayload['items'] ?? [];

    if (empty($advantagesItems)) {
        $advantagesItems = [
            ['icon' => 'bi-shield-check', 'title_ar' => 'الأمان', 'title_en' => 'Safety', 'description_ar' => 'معايير تعقيم صارمة وبروتوكولات واضحة داخل العيادات لضمان أعلى مستويات الأمان والجودة.', 'description_en' => 'Strict sterilization standards and clear protocols to ensure top safety and quality.'],
            ['icon' => 'bi-cpu', 'title_ar' => 'التطور', 'title_en' => 'Modern Technology', 'description_ar' => 'تطبيق أحدث الممارسات واستخدام أجهزة متقدمة للتشخيص والعلاج لضمان أفضل النتائج.', 'description_en' => 'Modern diagnostics and treatment technologies for better results.'],
            ['icon' => 'bi-award', 'title_ar' => 'الخبرة', 'title_en' => 'Experience', 'description_ar' => 'كوادر طبية متخصصة بخبرات عملية وتدريب مستمر لتقديم نتائج دقيقة وآمنة.', 'description_en' => 'Specialized dentists with continuous training and real clinical experience.'],
            ['icon' => 'bi-people', 'title_ar' => 'المسؤولية', 'title_en' => 'Responsibility', 'description_ar' => 'متابعة دقيقة لكل حالة من أول زيارة حتى اكتمال العلاج مع اهتمام حقيقي بكل التفاصيل.', 'description_en' => 'Close follow-up from the first visit to treatment completion.'],
            ['icon' => 'bi-gem', 'title_ar' => 'الفخامة', 'title_en' => 'Premium Experience', 'description_ar' => 'بيئة مريحة وتصميم عصري وتجربة استقبال منظمة تمنح المريض شعورًا بالثقة والاطمئنان.', 'description_en' => 'A comfortable modern environment with an organized patient journey.'],
            ['icon' => 'bi-geo-alt', 'title_ar' => 'تميز الموقع', 'title_en' => 'Prime Location', 'description_ar' => 'موقع مركزي سهل الوصول مع فروع متعددة لتقديم الرعاية بالقرب منك.', 'description_en' => 'Central and easy-to-reach branches near you.'],
        ];
    }

    $advantagesStats = [
        [
            'value' => str_pad((string) count($advantagesItems), 2, '0', STR_PAD_LEFT),
            'label_ar' => 'نقاط قوة',
            'label_en' => 'Core strengths',
        ],
        [
            'value' => ($featuredDoctors->count() + 10) . '+',
            'label_ar' => 'كوادر متخصصة',
            'label_en' => 'Specialists',
        ],
        [
            'value' => max($branches->count(), 1),
            'label_ar' => 'فروع قريبة',
            'label_en' => 'Nearby branches',
        ],
    ];

    $advantagesNotes = [
        ['ar' => 'رحلة علاج أكثر وضوحًا', 'en' => 'A clearer treatment journey'],
        ['ar' => 'تشخيص أدق وقرار أسرع', 'en' => 'Sharper diagnostics, faster decisions'],
        ['ar' => 'بروتوكولات ثابتة داخل العيادات', 'en' => 'Consistent in-clinic protocols'],
        ['ar' => 'متابعة مستمرة بعد الزيارة', 'en' => 'Continuous follow-up after visits'],
        ['ar' => 'تفاصيل تجربة محسوبة', 'en' => 'A deliberately crafted experience'],
        ['ar' => 'وصول أسهل وخدمة أقرب', 'en' => 'Easier access and closer care'],
    ];

    $extractCoords = static function (?string $url): ?array {
        if (! $url) {
            return null;
        }

        $decoded = urldecode($url);
        $patterns = [
            '/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/',
            '/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/',
            '/[?&](?:q|query|ll)=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $decoded, $matches)) {
                return ['lat' => (float) $matches[1], 'lng' => (float) $matches[2]];
            }
        }

        return null;
    };

    $branchMapPoints = $branches->map(function ($branch) use ($extractCoords, $isAr) {
        $coords = $extractCoords($branch->google_maps_url);

        return [
            'id' => $branch->id,
            'name' => $isAr ? ($branch->name_ar ?: $branch->name_en) : ($branch->name_en ?: $branch->name_ar),
            'address' => $isAr ? ($branch->address_ar ?: $branch->address_en) : ($branch->address_en ?: $branch->address_ar),
            'phone' => $branch->phone,
            'maps_url' => $branch->google_maps_url,
            'lat' => $coords['lat'] ?? null,
            'lng' => $coords['lng'] ?? null,
        ];
    })->values();

    $validMapPoints = $branchMapPoints->filter(fn (array $point): bool => ! is_null($point['lat']) && ! is_null($point['lng']))->values();
    $mapCenter = $validMapPoints->first() ?: ['lat' => 30.0444, 'lng' => 31.2357];
@endphp

<style>
    @import url('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    .promo-banner-wrap {
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 24px 45px rgba(16, 82, 92, 0.14);
    }
    .promo-banner-item {
        min-height: 420px;
        background-size: cover;
        background-position: center;
        position: relative;
        color: #fff;
    }
    .promo-banner-item::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(100deg, rgba(13, 95, 115, 0.9) 0%, rgba(15, 139, 141, 0.78) 42%, rgba(7, 40, 49, 0.38) 100%);
    }
    .promo-banner-content {
        position: relative;
        z-index: 1;
        max-width: 680px;
        padding: 2.4rem;
    }
    .promo-badge {
        background: rgba(223, 247, 242, 0.18);
        border: 1px solid rgba(223, 247, 242, 0.42);
        border-radius: 99px;
        font-size: .85rem;
        font-weight: 600;
        padding: .35rem .75rem;
        display: inline-block;
        margin-bottom: 1rem;
    }
    .offer-card {
        border: 1px solid #d9e2ec;
        border-radius: 1rem;
        overflow: hidden;
        background: #fff;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .offer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }
    .offer-card img {
        width: 100%;
        height: 215px;
        object-fit: cover;
    }
    .offer-discount {
        background: linear-gradient(135deg, #ff8b25, #ff5a1f);
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
        padding: .3rem .6rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
    }
    .offer-price {
        color: #1d4f88;
        font-weight: 700;
        font-size: 1.35rem;
        line-height: 1;
    }
    .featured-services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1.1rem;
    }
    .service-pro-card {
        border: 1px solid #d5e4f3;
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f5faff 100%);
        box-shadow: 0 12px 28px rgba(19, 61, 112, .09);
        padding: 0;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .service-pro-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(380px 130px at 100% -10%, rgba(56, 127, 216, .15), transparent 55%);
        pointer-events: none;
    }
    .service-pro-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 34px rgba(19, 61, 112, .15);
    }
    .service-card-head {
        padding: .9rem .95rem 0 .95rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .6rem;
    }
    .service-badge {
        border: 1px solid #d2e4f8;
        color: #1f4d88;
        background: #eef6ff;
        border-radius: 999px;
        font-size: .73rem;
        font-weight: 700;
        padding: .15rem .55rem;
    }
    .service-icon-chip {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: linear-gradient(135deg, #d2e7ff, #e9f4ff);
        color: #1f4d88;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.22rem;
        animation: serviceFloat 2.2s ease-in-out infinite;
        box-shadow: inset 0 0 0 1px #cae1f8;
    }
    @keyframes serviceFloat {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
    }
    .service-thumb {
        width: 100%;
        height: 145px;
        object-fit: cover;
        border-top: 1px solid #e4edf7;
        border-bottom: 1px solid #e4edf7;
        margin: .7rem 0;
    }
    .service-card-body {
        padding: 0 .95rem .95rem .95rem;
        position: relative;
        z-index: 1;
    }
    .service-title {
        margin-bottom: .35rem;
        color: #133560;
        font-size: 1.12rem;
        font-weight: 800;
    }
    .service-link {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        text-decoration: none;
        border: 1px solid #cfe2f6;
        color: #1c4d89;
        background: #f2f8ff;
        border-radius: .65rem;
        padding: .28rem .6rem;
        font-size: .86rem;
        font-weight: 700;
    }
    .service-link:hover {
        background: #e7f2ff;
    }
    .advantages-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    .advantage-card {
        border: 1px solid #d5e4f3;
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        box-shadow: 0 10px 24px rgba(19, 61, 112, .08);
        padding: 1rem 1.1rem;
        min-height: 190px;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .advantage-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 30px rgba(19, 61, 112, .14);
    }
    .advantage-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #e3f0ff, #f1f8ff);
        border: 1px solid #d3e5f8;
        color: #1f4d88;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.22rem;
        margin-bottom: .8rem;
    }
    .advantage-title {
        font-size: 1.55rem;
        font-weight: 800;
        color: #0f2f52;
        margin-bottom: .45rem;
    }
    .advantage-text {
        color: #4f647c;
        line-height: 1.85;
        margin-bottom: 0;
    }
    .doctors-row {
        display: flex;
        gap: 1.1rem;
        overflow-x: auto;
        padding: .15rem 0 .55rem 0;
        scrollbar-width: thin;
    }
    .doctors-row::-webkit-scrollbar {
        height: 6px;
    }
    .doctors-row::-webkit-scrollbar-thumb {
        background: #c8d8ea;
        border-radius: 999px;
    }
    .doctor-pro-card {
        min-width: 280px;
        max-width: 280px;
        border: 1px solid #d5e3f2;
        border-radius: 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        box-shadow: 0 14px 28px rgba(18, 50, 90, .1);
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
        position: relative;
    }
    .doctor-pro-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 34px rgba(18, 50, 90, .14);
    }
    .doctor-photo-wrap {
        position: relative;
    }
    .doctor-photo {
        width: 100%;
        height: 205px;
        object-fit: cover;
        background: #ecf3fb;
    }
    .doctor-overlay {
        position: absolute;
        inset: auto 0 0 0;
        background: linear-gradient(180deg, transparent, rgba(8, 26, 49, .66));
        padding: .55rem .7rem;
        color: #fff;
        font-size: .78rem;
    }
    .doctor-badge {
        position: absolute;
        top: .7rem;
        right: .7rem;
        border: 1px solid rgba(255,255,255,.5);
        border-radius: 999px;
        background: rgba(15, 56, 103, .72);
        color: #fff;
        padding: .15rem .5rem;
        font-size: .72rem;
        font-weight: 700;
        backdrop-filter: blur(3px);
    }
    [dir="rtl"] .doctor-badge {
        right: auto;
        left: .7rem;
    }
    .doctor-pro-body {
        padding: .92rem;
    }
    .doctor-name {
        margin-bottom: .22rem;
        color: #14355c;
        font-weight: 800;
        font-size: 1.18rem;
    }
    .doctor-specialty {
        color: #56708d;
        margin-bottom: .48rem;
    }
    .doctor-exp {
        font-size: .8rem;
        color: #3f658f;
        margin-bottom: .6rem;
        border: 1px solid #d6e5f5;
        background: #f4f9ff;
        border-radius: 999px;
        padding: .2rem .55rem;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }
    .doctor-bio {
        font-size: .86rem;
        color: #4f6782;
        min-height: 40px;
        margin-bottom: .65rem;
    }
    .doctor-actions {
        display: flex;
        gap: .45rem;
    }
    .doctor-actions .btn {
        flex: 1;
        border-radius: .62rem;
        font-size: .82rem;
        font-weight: 700;
    }
    .branches-hours-wrap {
        position: relative;
        border: 1px solid #d7e6f5;
        border-radius: 1.5rem;
        background:
            radial-gradient(circle at top right, rgba(65, 132, 214, .12), transparent 26%),
            linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        padding: 1.1rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
        overflow: hidden;
    }
    .branches-hours-wrap::after {
        content: '';
        position: absolute;
        inset: auto auto -70px -60px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(71, 141, 223, .12), transparent 70%);
        pointer-events: none;
    }
    .branches-hours-wrap > * {
        position: relative;
        z-index: 1;
    }
    .branches-intro-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(260px, .85fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .branches-hero-card,
    .branches-quick-card {
        border: 1px solid #d7e5f4;
        border-radius: 1.35rem;
        background: rgba(255,255,255,.85);
        padding: 1rem 1.05rem;
        box-shadow: 0 12px 30px rgba(18, 56, 98, .07);
    }
    .branches-hero-card {
        background:
            linear-gradient(135deg, rgba(13, 95, 115, .96), rgba(15, 139, 141, .9)),
            #0f5f73;
        color: #fff;
        border-color: rgba(255,255,255,.15);
    }
    .branches-hero-top {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: .9rem;
        margin-bottom: .85rem;
    }
    .branches-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .8rem;
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.18);
        color: #eef6ff;
        font-size: .76rem;
        font-weight: 800;
    }
    .branches-hero-title {
        margin: 0;
        font-size: clamp(1.35rem, 1.15rem + .65vw, 1.9rem);
        font-weight: 900;
        line-height: 1.25;
    }
    .branches-hero-copy {
        margin: 0;
        color: #deebfb;
        line-height: 1.9;
        max-width: 62ch;
    }
    .branches-hero-pills {
        display: flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }
    .branches-hero-pills span {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .34rem .72rem;
        border-radius: 999px;
        background: rgba(255,255,255,.11);
        border: 1px solid rgba(255,255,255,.16);
        color: #f6fbff;
        font-size: .78rem;
        font-weight: 700;
    }
    .branches-quick-card h6 {
        margin: 0 0 .35rem;
        color: #123b67;
        font-size: .98rem;
        font-weight: 800;
    }
    .branches-quick-card p {
        margin: 0;
        color: #67809a;
        line-height: 1.8;
        font-size: .88rem;
    }
    .branches-top-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
        margin-bottom: 1rem;
    }
    .branch-stat {
        border: 1px solid #d7e5f4;
        border-radius: 1.05rem;
        background: rgba(255,255,255,.94);
        padding: .8rem .85rem;
        text-align: center;
        box-shadow: 0 12px 24px rgba(17, 54, 97, .06);
    }
    .branch-stat-num {
        color: #15636e;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1;
    }
    .branch-stat-label {
        color: #5e7792;
        font-size: .8rem;
        margin-top: .28rem;
    }
    .branch-row {
        border: 1px solid #dbe7f3;
        border-radius: .95rem;
        padding: .78rem .85rem;
        background: #fff;
        margin-bottom: .6rem;
        box-shadow: 0 8px 18px rgba(21, 60, 104, .04);
    }
    .branch-row-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .7rem;
    }
    .branch-row-day {
        color: #103861;
        font-weight: 800;
    }
    .branch-row-time {
        color: #1c507f;
        font-size: .92rem;
        font-weight: 700;
    }
    .branch-row-note {
        margin-top: .42rem;
        color: #6b8198;
        font-size: .79rem;
    }
    .branch-map-wrap {
        border: 1px solid #d7e5f3;
        border-radius: 1.2rem;
        overflow: hidden;
        background: #fff;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.7);
    }
    .branch-map-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .65rem;
        padding: .9rem 1rem;
        border-bottom: 1px solid #e4edf6;
        background: linear-gradient(180deg, #f8fbff 0%, #f4f9ff 100%);
    }
    .branch-map-header h6 {
        color: #184f63;
        font-weight: 800;
    }
    .branch-map-subtitle {
        color: #6a8098;
        font-size: .8rem;
    }
    .branch-map {
        width: 100%;
        height: 355px;
        border-bottom: 1px solid #e4edf6;
    }
    .branch-map-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
        max-height: 285px;
        overflow: auto;
        padding: .9rem;
    }
    .branch-map-item {
        border: 1px solid #d7e5f3;
        border-radius: 1rem;
        padding: .9rem;
        background: #fff;
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        display: flex;
        flex-direction: column;
        gap: .55rem;
    }
    .branch-map-item:hover {
        border-color: #9ec2e7;
        box-shadow: 0 8px 20px rgba(28, 74, 126, .11);
        transform: translateY(-2px);
    }
    .branch-map-item.active {
        border-color: #3e87d8;
        box-shadow: 0 0 0 2px rgba(62, 135, 216, .13);
        background: #f4f9ff;
    }
    .branch-map-item:last-child {
        margin-bottom: 0;
    }
    .branch-map-topline {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: .65rem;
    }
    .branch-map-titlegroup {
        display: flex;
        flex-direction: column;
        gap: .3rem;
        min-width: 0;
    }
    .branch-map-item h6 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: .35rem;
        color: #113a65;
        font-weight: 800;
    }
    .branch-map-address {
        color: #5f7893;
        line-height: 1.8;
        font-size: .88rem;
    }
    .branch-map-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .55rem;
    }
    .branch-meta-box {
        display: flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .65rem;
        border: 1px solid #dce8f4;
        border-radius: .8rem;
        background: #f8fbff;
        color: #54708e;
        font-size: .8rem;
    }
    .branch-meta-box i {
        color: #1c4d89;
        font-size: .92rem;
    }
    .branch-meta-box strong {
        display: block;
        color: #103c68;
        font-size: .84rem;
    }
    .branch-map-item small {
        display: block;
        color: #637991;
    }
    .branch-mini-chip {
        display: inline-flex;
        align-items: center;
        gap: .28rem;
        padding: .22rem .55rem;
        border-radius: 999px;
        background: #f3f8fd;
        border: 1px solid #d8e5f4;
        color: #5c7793;
        font-size: .72rem;
        font-weight: 700;
    }
    .branch-map-actions {
        display: flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
        margin-top: .1rem;
    }
    .branch-map-link {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: #12676d;
        font-size: .8rem;
        font-weight: 700;
    }
    .branch-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .38rem;
        min-height: 2.35rem;
        padding: 0 .9rem;
        border-radius: 999px;
        border: 1px solid #d5e3f4;
        background: #f7fbff;
        color: #1c4d89;
        font-size: .8rem;
        font-weight: 800;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .branch-action-btn:hover {
        transform: translateY(-1px);
        border-color: #9fc4e8;
        box-shadow: 0 10px 20px rgba(22, 63, 109, .08);
        color: #17457a;
    }
    .branch-action-btn-primary {
        background: linear-gradient(135deg, #0f8b8d, #1ba79d);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 12px 24px rgba(15, 139, 141, .18);
    }
    .branch-action-btn-primary:hover {
        color: #fff;
        border-color: transparent;
    }
    .hour-row-pro {
        border: 1px solid #dfeaf5;
        border-radius: .9rem;
        padding: .5rem .6rem;
        margin-bottom: .45rem;
        background: #fff;
    }
    .hour-badge-open {
        background: #e9f9ef;
        color: #0f7a3e;
        border: 1px solid #b9ebca;
        border-radius: 999px;
        font-size: .75rem;
        padding: .1rem .55rem;
    }
    .hour-badge-closed {
        background: #fff1f2;
        color: #b42318;
        border: 1px solid #fecdd3;
        border-radius: 999px;
        font-size: .75rem;
        padding: .1rem .55rem;
    }
    .section-headline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .8rem;
        margin-bottom: 1rem;
    }
    .section-kicker {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border: 1px solid #cfe0f2;
        background: #f4f8fd;
        color: #1e4e87;
        border-radius: 999px;
        font-size: .78rem;
        padding: .2rem .65rem;
        font-weight: 600;
    }
    .testimonial-card {
        border: 1px solid #d7e4f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 12px 26px rgba(17, 55, 100, .09);
        padding: 1rem 1rem .9rem 1rem;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .testimonial-card::after {
        content: '\201D';
        position: absolute;
        top: -6px;
        left: 12px;
        font-size: 3rem;
        color: #dce9f7;
        font-weight: 700;
        line-height: 1;
    }
    [dir="rtl"] .testimonial-card::after {
        right: 12px;
        left: auto;
    }
    .testimonial-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 34px rgba(17, 55, 100, .14);
    }
    .testimonial-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .75rem;
        gap: .6rem;
        border-bottom: 1px dashed #d8e6f4;
        padding-bottom: .6rem;
    }
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: .7rem;
        position: relative;
        z-index: 1;
    }
    .testimonial-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #d7e7f8;
        background: #eef4fb;
    }
    .testimonial-name {
        font-weight: 700;
        margin-bottom: 0;
        font-size: .96rem;
        color: #1a3a5f;
    }
    .testimonial-label {
        font-size: .76rem;
        color: #6a7f96;
    }
    .testimonial-stars {
        white-space: nowrap;
        font-size: .95rem;
        line-height: 1;
        letter-spacing: 1px;
    }
    .testimonial-stars .on {
        color: #f6b21a;
    }
    .testimonial-stars .off {
        color: #d2ddeb;
    }
    .testimonial-text {
        color: #2c3f56;
        margin-bottom: .55rem;
        position: relative;
        z-index: 1;
        line-height: 1.8;
    }
    .testimonial-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: .75rem;
        color: #6b8098;
        border-top: 1px dashed #d8e6f4;
        padding-top: .55rem;
    }
    .testimonial-footer .chip {
        border: 1px solid #d4e4f5;
        border-radius: 999px;
        padding: .12rem .5rem;
        background: #f4f9ff;
        color: #33587d;
        font-weight: 600;
    }
    .testimonials-pro {
        border: 1px solid #d5e5f5;
        border-radius: 1rem;
        padding: 1rem;
        background: linear-gradient(145deg, #eef5fc 0%, #f8fbff 100%);
    }
    .post-card {
        border: 1px solid #d8e4f0;
        border-radius: 1rem;
        position: relative;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 12px 26px rgba(22, 59, 106, .08);
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .post-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(22, 59, 106, .13);
    }
    .post-thumb {
        width: 100%;
        height: 190px;
        object-fit: cover;
        background: #e9f1fb;
    }
    .post-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: .6rem;
        flex: 1;
    }
    .post-date-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border: 1px solid #d5e4f3;
        border-radius: 999px;
        padding: .15rem .6rem;
        font-size: .76rem;
        color: #4a6482;
        width: fit-content;
    }
    .post-title {
        font-size: 1.08rem;
        font-weight: 700;
        margin: 0;
        color: #102f55;
        min-height: 52px;
    }
    .post-excerpt {
        color: #58708a;
        margin-bottom: 0;
        line-height: 1.7;
        flex: 1;
    }
    .post-read-btn {
        border-radius: .7rem;
        font-weight: 600;
    }
    .faq-pro-wrap {
        border: 1px solid #d9e5f1;
        border-radius: 1rem;
        background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
        padding: 1rem;
    }
    .faq-pro-wrap .accordion-item {
        border: 1px solid #d9e6f3;
        border-radius: .85rem;
        overflow: hidden;
        margin-bottom: .65rem;
        background: #fff;
    }
    .faq-pro-wrap .accordion-item:last-child {
        margin-bottom: 0;
    }
    .faq-pro-wrap .accordion-button {
        font-weight: 700;
        color: #12365f;
        background: #fff;
        box-shadow: none;
        padding: .95rem 1.1rem;
    }
    .faq-pro-wrap .accordion-button:not(.collapsed) {
        color: #0e3f74;
        background: #f1f7ff;
    }
    .faq-pro-wrap .accordion-button:focus {
        border-color: #9fc3ea;
        box-shadow: 0 0 0 .2rem rgba(52, 123, 208, .12);
    }
    .faq-pro-wrap .accordion-body {
        color: #445f7b;
        line-height: 1.9;
        background: #fff;
    }
    .cta-pro-card {
        border: 1px solid #d2e3f4;
        border-radius: 1.1rem;
        background: linear-gradient(130deg, #f7fbff 0%, #eef5fd 100%);
        box-shadow: 0 14px 30px rgba(19, 61, 112, .1);
        padding: 1.4rem;
        text-align: center;
    }
    .cta-pro-title {
        margin-bottom: .35rem;
        color: #11345a;
        font-weight: 800;
    }
    .cta-pro-text {
        color: #5d7792;
        margin-bottom: 1rem;
    }
    .cta-pro-actions {
        display: flex;
        justify-content: center;
        gap: .55rem;
        flex-wrap: wrap;
    }
    .cta-pro-btn {
        min-width: 180px;
        border-radius: .75rem;
        font-weight: 700;
    }
    .section-shell {
        position: relative;
        margin-bottom: 1.6rem;
        padding: 1.5rem;
        border: 1px solid #d4e8e2;
        border-radius: 1.8rem;
        background: linear-gradient(180deg, rgba(255,255,255,.96) 0%, rgba(246,252,250,.92) 100%);
        box-shadow: 0 18px 38px rgba(16, 82, 92, .08);
        overflow: hidden;
    }
    .section-shell::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 140px;
        background: radial-gradient(420px 140px at 100% 0, rgba(72, 184, 175, .14), transparent 65%);
        pointer-events: none;
    }
    .section-shell > * {
        position: relative;
        z-index: 1;
    }
    .section-shell-soft {
        background: linear-gradient(180deg, rgba(240,250,247,.96) 0%, rgba(255,255,255,.95) 100%);
    }
    .section-shell-plain {
        padding: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
    }
    .section-shell-plain::before {
        display: none;
    }
    .section-headline-pro {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.15rem;
    }
    .section-headline-pro h3 {
        margin: .55rem 0 0;
        color: #184b61;
        font-size: clamp(1.75rem, 1.5rem + .6vw, 2.25rem);
        font-weight: 800;
    }
    .section-summary {
        margin: .35rem 0 0;
        color: #647f88;
        max-width: 620px;
        line-height: 1.8;
    }
    .section-chipline {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .36rem .8rem;
        border: 1px solid #d4e8e2;
        border-radius: 999px;
        background: rgba(239,250,247,.92);
        color: #1a716c;
        font-size: .78rem;
        font-weight: 700;
    }
    .section-headline-actions {
        display: flex;
        align-items: center;
        gap: .65rem;
        flex-wrap: wrap;
    }
    .section-ghost-link {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: #1c4d89;
        font-weight: 700;
    }
    .offers-grid-pro {
        display: grid;
        grid-template-columns: repeat(12, minmax(0, 1fr));
        gap: 1rem;
    }
    .offer-card-pro {
        grid-column: span 3;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        border: 1px solid #d6e8e3;
        border-radius: 1.45rem;
        background: linear-gradient(180deg, #ffffff 0%, #f6fcfa 100%);
        box-shadow: 0 16px 34px rgba(16, 82, 92, .09);
        overflow: hidden;
        transition: transform .24s ease, box-shadow .24s ease;
    }
    .offer-card-pro:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 42px rgba(16, 82, 92, .14);
    }
    .offer-visual {
        position: relative;
        height: 236px;
        overflow: hidden;
    }
    .offer-visual::after {
        content: '';
        position: absolute;
        inset: auto 0 0;
        height: 92px;
        background: linear-gradient(180deg, transparent, rgba(10, 30, 55, .55));
    }
    .offer-media {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .offer-card-pro:hover .offer-media {
        transform: scale(1.05);
    }
    .offer-floating {
        position: absolute;
        top: 1rem;
        left: 1rem;
        right: 1rem;
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: .75rem;
        z-index: 1;
    }
    [dir="rtl"] .offer-floating {
        flex-direction: row-reverse;
    }
    .offer-tagline,
    .offer-until {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .34rem .72rem;
        border-radius: 999px;
        backdrop-filter: blur(6px);
        font-size: .74rem;
        font-weight: 700;
    }
    .offer-tagline {
        background: rgba(255,255,255,.92);
        color: #194977;
        border: 1px solid rgba(255,255,255,.8);
    }
    .offer-until {
        background: rgba(13, 95, 115, .68);
        color: #fff;
        border: 1px solid rgba(255,255,255,.18);
    }
    .offer-discount-pro {
        position: absolute;
        right: 1rem;
        bottom: 1rem;
        z-index: 1;
        background: linear-gradient(135deg, #1bb6a6, #0f8b8d);
        color: #fff;
        border-radius: 999px;
        padding: .45rem .9rem;
        font-size: .82rem;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(15, 139, 141, .24);
    }
    [dir="rtl"] .offer-discount-pro {
        right: auto;
        left: 1rem;
    }
    .offer-body-pro {
        display: flex;
        flex-direction: column;
        gap: .9rem;
        flex: 1;
        padding: 1rem;
    }
    .offer-title-pro {
        margin: 0;
        color: #11345a;
        font-size: 1.2rem;
        font-weight: 800;
    }
    .offer-copy {
        margin: 0;
        color: #67809b;
        line-height: 1.8;
        font-size: .92rem;
    }
    .offer-meta-pro {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
        margin-top: auto;
    }
    .offer-price-wrap {
        display: flex;
        flex-direction: column;
        gap: .15rem;
    }
    .offer-price-caption {
        color: #7390ae;
        font-size: .75rem;
        font-weight: 700;
    }
    .offer-price-pro {
        color: #13656b;
        font-size: 2rem;
        line-height: 1;
        font-weight: 900;
    }
    .offer-price-pro small {
        font-size: .95rem;
        color: #6b8890;
    }
    .service-pro-card {
        padding: 1.05rem;
        border-radius: 1.45rem;
    }
    .service-card-head {
        padding: 0;
        margin-bottom: .9rem;
    }
    .service-thumb {
        height: 180px;
        margin: 0 0 .95rem;
        border-radius: 1rem;
        border: 0;
    }
    .service-card-body {
        padding: 0;
    }
    .service-statline {
        display: flex;
        align-items: center;
        gap: .45rem;
        flex-wrap: wrap;
        margin-top: .85rem;
        color: #64829e;
        font-size: .8rem;
        font-weight: 700;
    }
    .service-statline span {
        display: inline-flex;
        align-items: center;
        gap: .28rem;
        padding: .22rem .58rem;
        border: 1px solid #d7e8e3;
        border-radius: 999px;
        background: #f1faf7;
    }
    .advantages-grid-pro {
        display: grid;
        grid-template-columns: minmax(260px, .95fr) minmax(0, 2.05fr);
        gap: 1rem;
    }
    .advantages-intro-pro {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-height: 100%;
        padding: 1.25rem;
        border: 1px solid #d1e7e2;
        border-radius: 1.7rem;
        background:
            radial-gradient(circle at top left, rgba(126, 214, 198, .22), transparent 30%),
            radial-gradient(circle at bottom right, rgba(255,255,255,.12), transparent 34%),
            linear-gradient(180deg, #0f5f73 0%, #0f8b8d 55%, #27a997 100%);
        box-shadow: 0 20px 40px rgba(16, 82, 92, .14);
        overflow: hidden;
    }
    .advantages-intro-pro::after {
        content: '';
        position: absolute;
        inset: auto -30px -42px auto;
        width: 180px;
        height: 180px;
        border-radius: 36px;
        background: linear-gradient(135deg, rgba(255,255,255,.16), rgba(255,255,255,0));
        transform: rotate(22deg);
        pointer-events: none;
    }
    .advantages-intro-top {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: .9rem;
    }
    .advantages-badge-pro {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .42rem .85rem;
        border-radius: 999px;
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.24);
        color: #fff;
        font-size: .76rem;
        font-weight: 800;
        backdrop-filter: blur(8px);
    }
    .advantages-kicker {
        margin: 0;
        color: #d7e7fb;
        font-size: .78rem;
        font-weight: 700;
    }
    .advantages-intro-title {
        margin: .35rem 0 0;
        color: #fff;
        font-size: clamp(1.8rem, 1.35rem + 1.1vw, 2.55rem);
        line-height: 1.18;
        font-weight: 900;
        max-width: 12ch;
    }
    .advantages-intro-copy {
        margin: 0;
        color: #e5effb;
        line-height: 1.95;
        max-width: 34ch;
        font-size: .96rem;
    }
    .advantages-statline {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .7rem;
    }
    .advantages-stat {
        padding: .85rem .8rem;
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 1.15rem;
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(8px);
        text-align: center;
    }
    .advantages-stat strong {
        display: block;
        color: #fff;
        font-size: 1.28rem;
        line-height: 1;
        font-weight: 900;
    }
    .advantages-stat span {
        display: block;
        margin-top: .35rem;
        color: #dbebff;
        font-size: .76rem;
        font-weight: 700;
    }
    .advantages-intro-actions {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
        margin-top: auto;
    }
    .advantages-intro-btn {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .8rem 1rem;
        border-radius: 999px;
        background: #fff;
        color: #12676d;
        font-weight: 800;
        box-shadow: 0 14px 26px rgba(16, 82, 92, .16);
    }
    .advantages-intro-link {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: #f4f8ff;
        font-weight: 700;
        padding: .55rem .15rem;
    }
    .advantages-cards-pro {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }
    .advantage-card-pro {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: .95rem;
        min-height: 100%;
        border: 1px solid #d5e8e2;
        border-radius: 1.65rem;
        background:
            radial-gradient(circle at top right, rgba(72, 184, 175, .12), transparent 30%),
            linear-gradient(180deg, #ffffff 0%, #f6fcfa 100%);
        box-shadow: 0 16px 34px rgba(16, 82, 92, .08);
        padding: 1.25rem;
        overflow: hidden;
        transition: transform .24s ease, box-shadow .24s ease, border-color .24s ease;
    }
    .advantage-card-pro::after {
        content: '';
        position: absolute;
        inset: auto -35px -35px auto;
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(72, 184, 175, .12), transparent 65%);
    }
    .advantage-card-pro:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 42px rgba(16, 82, 92, .12);
        border-color: #bfe1d8;
    }
    .advantage-card-top {
        display: flex;
        align-items: start;
        justify-content: space-between;
        gap: .75rem;
    }
    .advantage-icon-pro {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: linear-gradient(135deg, #dff7f2, #ffffff);
        border: 1px solid #cce6e0;
        color: #12676d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.88);
    }
    .advantage-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 3.15rem;
        height: 2.1rem;
        padding: 0 .75rem;
        border-radius: 999px;
        background: #edf9f6;
        color: #54797d;
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .04em;
    }
    .advantage-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .24rem .58rem;
        border-radius: 999px;
        background: #f1faf7;
        color: #618181;
        font-size: .72rem;
        font-weight: 800;
    }
    .advantage-title-pro {
        margin: 0;
        color: #184b61;
        font-size: 1.32rem;
        font-weight: 800;
        line-height: 1.35;
    }
    .advantage-text-pro {
        margin: 0;
        color: #5f787f;
        line-height: 1.9;
    }
    .advantage-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: .85rem;
        margin-top: auto;
        border-top: 1px dashed #d7e8e3;
    }
    .advantage-note {
        color: #5c7a80;
        font-size: .82rem;
        font-weight: 700;
    }
    .advantage-arrow {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 999px;
        background: linear-gradient(135deg, #eaf8f5, #ffffff);
        border: 1px solid #d5e7e2;
        color: #12676d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .doctors-grid-pro {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    .doctor-pro-card {
        min-width: 0;
        max-width: none;
        border-radius: 1.55rem;
    }
    .doctor-photo {
        height: 260px;
    }
    .doctor-pro-body {
        padding: 1rem;
    }
    .doctor-meta-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .6rem;
        margin-bottom: .7rem;
        color: #668084;
        font-size: .78rem;
    }
    .doctor-feature {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .24rem .58rem;
        border: 1px solid #d6e8e2;
        border-radius: 999px;
        background: #f1faf7;
        color: #2d6768;
        font-weight: 700;
    }
    .doctor-bio {
        min-height: 70px;
        line-height: 1.8;
    }
    .testimonials-grid-pro {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    .testimonial-card {
        padding: 1.15rem 1.15rem 1rem;
        border-radius: 1.45rem;
    }
    .testimonial-rating-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .2rem .55rem;
        border-radius: 999px;
        background: #fff6de;
        color: #b97a00;
        font-size: .76rem;
        font-weight: 800;
    }
    .post-grid-pro {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }
    .post-card {
        border-radius: 1.55rem;
    }
    .post-thumb {
        height: 240px;
    }
    .post-body {
        padding: 1.1rem;
    }
    .post-date-chip {
        background: #f2faf7;
        font-weight: 700;
    }
    .post-title {
        min-height: auto;
        font-size: 1.2rem;
        font-weight: 800;
    }
    .branches-layout-pro {
        display: grid;
        grid-template-columns: 1.02fr 1.25fr;
        gap: 1rem;
        align-items: stretch;
    }
    .branches-hours-wrap {
        padding: 1.1rem;
        border-radius: 1.7rem;
    }
    .branch-panel-card {
        height: 100%;
        border: 1px solid #d8e8e3;
        border-radius: 1.35rem;
        background: rgba(255,255,255,.86);
        padding: 1rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.55);
    }
    .faq-pro-wrap {
        padding: 1.1rem;
        border-radius: 1.55rem;
    }
    .cta-pro-card {
        border-radius: 1.7rem;
        padding: 1.8rem 1.35rem;
    }
    @media (max-width: 991.98px) {
        .promo-banner-item { min-height: 360px; }
        .promo-banner-content { padding: 1.6rem; }
        .post-thumb { height: 170px; }
        .branches-top-stats { grid-template-columns: 1fr; }
        .advantages-grid { grid-template-columns: 1fr; }
        .section-shell {
            padding: 1.15rem;
            border-radius: 1.35rem;
        }
        .section-headline-pro {
            align-items: start;
            flex-direction: column;
        }
        .offers-grid-pro,
        .advantages-grid-pro,
        .doctors-grid-pro,
        .testimonials-grid-pro,
        .post-grid-pro,
        .branches-layout-pro {
            grid-template-columns: 1fr;
        }
        .branches-intro-grid,
        .branch-map-list {
            grid-template-columns: 1fr;
        }
        .branch-map-meta {
            grid-template-columns: 1fr;
        }
        .advantages-cards-pro,
        .advantages-statline {
            grid-template-columns: 1fr;
        }
        .advantages-intro-pro {
            min-height: auto;
            padding: 1.15rem;
        }
        .advantages-intro-title {
            max-width: none;
            font-size: clamp(1.6rem, 1.3rem + 1vw, 2rem);
        }
        .advantages-intro-copy {
            max-width: none;
        }
        .advantages-intro-actions {
            margin-top: .25rem;
        }
        .offer-card-pro {
            grid-column: span 12;
        }
        .doctor-photo {
            height: 230px;
        }
    }
    @media (max-width: 767.98px) {
        .offer-meta-pro {
            flex-direction: column;
            align-items: start;
        }
        .post-thumb {
            height: 200px;
        }
        .advantages-intro-actions {
            flex-direction: column;
            align-items: stretch;
        }
        .advantages-intro-btn,
        .advantages-intro-link {
            justify-content: center;
            width: 100%;
            border-radius: 1rem;
        }
        .advantages-intro-link {
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.14);
        }
    }
    @media (max-width: 575.98px) {
        .hero-section-pro {
            padding: 1rem;
            border-radius: 1.15rem;
        }
        .hero-main-card {
            padding: 1rem;
            border-radius: 1.15rem;
        }
        .hero-main-title {
            font-size: clamp(1.7rem, 1.25rem + 2vw, 2.25rem);
            line-height: 1.2;
        }
        .hero-main-copy {
            font-size: .95rem;
            line-height: 1.85;
        }
        .hero-cta-group,
        .hero-contact-strip,
        .branches-hero-pills,
        .advantages-intro-actions,
        .section-headline-actions {
            width: 100%;
        }
        .hero-cta-group .btn,
        .section-headline-actions .btn,
        .advantages-intro-actions a {
            width: 100%;
            justify-content: center;
        }
        .hero-side-card,
        .promo-banner-item,
        .offer-card-pro,
        .advantage-card-pro,
        .doctor-pro-card,
        .testimonial-card,
        .post-card,
        .branch-panel-card,
        .branches-hero-card,
        .branches-quick-card {
            border-radius: 1.1rem;
        }
        .promo-banner-item {
            min-height: 320px;
        }
        .promo-banner-content {
            padding: 1rem;
        }
        .promo-banner-title {
            font-size: clamp(1.7rem, 1.3rem + 1.8vw, 2.1rem);
        }
        .section-shell {
            padding: 1rem;
            border-radius: 1.2rem;
        }
        .section-headline-pro h3 {
            font-size: clamp(1.45rem, 1.2rem + 1vw, 1.9rem);
        }
        .section-summary {
            font-size: .93rem;
            line-height: 1.8;
        }
        .offer-visual,
        .post-thumb,
        .doctor-photo {
            height: 200px;
        }
        .service-thumb {
            height: 170px;
        }
        .offer-body-pro,
        .doctor-pro-body,
        .post-body,
        .branches-hours-wrap,
        .advantages-intro-pro {
            padding: .95rem;
        }
        .offer-price-pro {
            font-size: 1.7rem;
        }
        .branch-map {
            height: 280px;
        }
        .branch-map-list {
            padding: .75rem;
        }
    }
</style>

<section class="hero mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-7">
            <h1 class="display-6 fw-bold mb-3">{{ $heroTitle }}</h1>
            <p class="mb-4">{{ $heroText }}</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-light text-primary">{{ $isAr ? 'احجز موعدك الآن' : 'Book your appointment' }}</a>
                <a href="{{ route('front.services.index', app()->getLocale()) }}" class="btn btn-outline-light">{{ $isAr ? 'تصفح الخدمات' : 'Browse services' }}</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-soft p-3 text-center">
                <h5 class="mb-2">{{ $isAr ? 'إحصائيات سريعة' : 'Quick stats' }}</h5>
                <div class="row text-center">
                    <div class="col-4"><div class="fs-4 fw-bold">{{ $featuredDoctors->count() + 10 }}+</div><small>{{ $isAr ? 'الأطباء' : 'Doctors' }}</small></div>
                    <div class="col-4"><div class="fs-4 fw-bold">{{ $featuredServices->count() + 20 }}+</div><small>{{ $isAr ? 'الخدمات' : 'Services' }}</small></div>
                    <div class="col-4"><div class="fs-4 fw-bold">{{ $branches->count() }}</div><small>{{ $isAr ? 'الفروع' : 'Branches' }}</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div id="promoBannerCarousel" class="carousel slide promo-banner-wrap" data-bs-ride="carousel" data-bs-interval="4500">
        <div class="carousel-indicators mb-2">
            @foreach($banners as $index => $banner)
                <button type="button" data-bs-target="#promoBannerCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($banners as $index => $banner)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="promo-banner-item" style="background-image: url('{{ $banner['bg_image'] ?? '' }}');">
                        <div class="promo-banner-content">
                            <span class="promo-badge">{{ $isAr ? ($banner['badge_ar'] ?? 'إعلان') : ($banner['badge_en'] ?? 'Ad') }}</span>
                            <h2 class="display-6 fw-bold mb-3">{{ $isAr ? ($banner['title_ar'] ?? '') : ($banner['title_en'] ?? '') }}</h2>
                            <p class="mb-4">{{ $isAr ? ($banner['subtitle_ar'] ?? '') : ($banner['subtitle_en'] ?? '') }}</p>
                            <div class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-light text-primary px-4" href="{{ route('front.appointments.create', app()->getLocale()) }}">{{ $isAr ? 'حجز موعد' : 'Book Appointment' }}</a>
                                @if(!empty($banner['phone']))
                                    <a class="btn btn-outline-light px-4" href="tel:{{ preg_replace('/[^0-9+]/', '', $banner['phone']) }}">{{ $banner['phone'] }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#promoBannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoBannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</section>

<section class="section-shell">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-megaphone"></i> {{ $isAr ? 'عروض علاجية حصرية' : 'Exclusive treatment offers' }}</span>
            <h3>{{ $isAr ? 'عروضنا المميزة لفترة محدودة' : 'Limited-time special offers' }}</h3>
            <p class="section-summary">{{ $isAr ? 'باقات مختارة لبدء العلاج أو التجميل بسعر أفضل، مع عرض أوضح للمحتوى والسعر والميزة الفعلية لكل خدمة.' : 'Selected care packages with clearer pricing, stronger presentation, and a more premium booking path.' }}</p>
        </div>
        <div class="section-headline-actions">
            <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز العرض الآن' : 'Book an offer now' }}</a>
        </div>
    </div>
    <div class="offers-grid-pro">
        @foreach($offers as $offer)
            @php
                $offerTitle = $isAr ? ($offer['title_ar'] ?? '') : ($offer['title_en'] ?? '');
                $offerDiscount = $isAr ? ($offer['discount_ar'] ?? '') : ($offer['discount_en'] ?? '');
                $offerImage = $offer['image'] ?? '';
                $offerDescription = $isAr
                    ? 'جلسة منظمة ضمن تجربة علاجية سريعة مع متابعة أولية من الفريق.'
                    : 'A streamlined treatment experience with guided follow-up from the clinic team.';
            @endphp
            <article class="offer-card-pro">
                <div class="offer-visual">
                    <img class="offer-media" src="{{ $offerImage }}" alt="{{ $offerTitle }}">
                    <div class="offer-floating">
                        <span class="offer-tagline"><i class="bi bi-stars"></i> {{ $isAr ? 'عرض مميز' : 'Featured offer' }}</span>
                        <span class="offer-until"><i class="bi bi-calendar3"></i> {{ $isAr ? 'حتى نهاية الشهر' : 'Until month-end' }}</span>
                    </div>
                    @if($offerDiscount)
                        <span class="offer-discount-pro">{{ $offerDiscount }}</span>
                    @endif
                </div>
                <div class="offer-body-pro">
                    <div>
                        <h4 class="offer-title-pro">{{ $offerTitle }}</h4>
                        <p class="offer-copy">{{ $offerDescription }}</p>
                    </div>
                    <div class="offer-meta-pro">
                        <div class="offer-price-wrap">
                            <span class="offer-price-caption">{{ $isAr ? 'السعر بعد العرض' : 'Offer price' }}</span>
                            <div class="offer-price-pro">{{ $offer['price'] ?? '' }} <small>{{ $offer['currency'] ?? '' }}</small></div>
                        </div>
                        <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-outline-primary">{{ $isAr ? 'احجز الآن' : 'Book now' }}</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="section-shell section-shell-soft">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-heart-pulse"></i> {{ $isAr ? 'خدمات منتقاة' : 'Curated services' }}</span>
            <h3>{{ $isAr ? 'الخدمات المميزة' : 'Featured Services' }}</h3>
            <p class="section-summary">{{ $isAr ? 'بطاقات أوضح للخدمات الأساسية مع إبراز الفئة وطريقة التقديم وسرعة الوصول لصفحة التفاصيل.' : 'Clearer presentation for key services with better hierarchy and faster access to detail pages.' }}</p>
        </div>
        <div class="section-headline-actions">
            <a href="{{ route('front.services.index', app()->getLocale()) }}" class="section-ghost-link">
                {{ $isAr ? 'عرض كل الخدمات' : 'View all services' }}
                <i class="bi {{ $isAr ? 'bi-arrow-left-short' : 'bi-arrow-right-short' }}"></i>
            </a>
        </div>
    </div>
    <div class="featured-services-grid">
        @foreach($featuredServices as $index => $s)
            @php
                $serviceIcon = $serviceIconMap[$s->slug] ?? ($serviceIconMap[$s->id] ?? $defaultServiceIcons[$index % count($defaultServiceIcons)]);
                $serviceImage = $resolveMediaUrl($s->image);
            @endphp
            <article class="service-pro-card">
                <div class="service-card-head">
                    <span class="service-badge">{{ $isAr ? 'خدمة مميزة' : 'Featured' }}</span>
                    <span class="service-icon-chip"><i class="bi {{ $serviceIcon }}"></i></span>
                </div>
                @if($serviceImage)
                    <img class="service-thumb" src="{{ $serviceImage }}" alt="{{ $s->title }}">
                @endif
                <div class="service-card-body">
                    <h5 class="service-title">{{ $s->title }}</h5>
                    <p class="text-secondary mb-2">{{ \Illuminate\Support\Str::limit($s->description, 95) }}</p>
                    <a class="service-link" href="{{ route('front.services.show', [app()->getLocale(), $s->slug]) }}">
                        {{ $isAr ? 'تفاصيل الخدمة' : 'Service details' }}
                        <i class="bi {{ $isAr ? 'bi-arrow-left-short' : 'bi-arrow-right-short' }}"></i>
                    </a>
                    <div class="service-statline">
                        <span><i class="bi bi-shield-check"></i> {{ $isAr ? 'رعاية آمنة' : 'Safe care' }}</span>
                        <span><i class="bi bi-lightning-charge"></i> {{ $isAr ? 'تشخيص سريع' : 'Quick assessment' }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="section-shell">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-patch-check"></i> {{ $isAr ? 'قيمة مضافة' : 'Why choose us' }}</span>
            <h3>{{ $advantagesTitle }}</h3>
            <p class="section-summary">{{ $isAr ? 'صياغة أحدث للمزايا الأساسية للمركز مع إبراز الخبرة والتقنيات والتجربة العامة للمريض.' : 'A more polished showcase of the clinic strengths, patient experience, and operational quality.' }}</p>
        </div>
    </div>
    <div class="advantages-grid-pro">
        <aside class="advantages-intro-pro">
            <div class="advantages-intro-top">
                <span class="advantages-badge-pro"><i class="bi bi-stars"></i> {{ $isAr ? 'تجربة علاج أوضح' : 'Sharper patient experience' }}</span>
                <p class="advantages-kicker">{{ $isAr ? 'نقاط الثقة الأساسية' : 'Confidence pillars' }}</p>
            </div>
            <div>
                <h4 class="advantages-intro-title">{{ $isAr ? 'المركز لا يقدّم خدمة فقط، بل تجربة علاج متكاملة.' : 'More than treatment. A more complete care experience.' }}</h4>
                <p class="advantages-intro-copy">{{ $isAr ? 'جمعنا الخبرة السريرية، الانضباط التشغيلي، والتقنيات الحديثة داخل مسار واضح يمنح المريض راحة أكبر وقرارًا أسرع ونتيجة أكثر ثباتًا.' : 'Clinical expertise, operational discipline, and modern technology come together in a clearer patient journey with stronger confidence and outcomes.' }}</p>
            </div>
            <div class="advantages-statline">
                @foreach($advantagesStats as $stat)
                    <div class="advantages-stat">
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $isAr ? $stat['label_ar'] : $stat['label_en'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="advantages-intro-actions">
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="advantages-intro-btn">
                    <i class="bi bi-calendar2-check"></i>
                    {{ $isAr ? 'احجز استشارتك' : 'Book a consultation' }}
                </a>
                <a href="{{ route('front.services.index', app()->getLocale()) }}" class="advantages-intro-link">
                    <i class="bi bi-arrow-up-left"></i>
                    {{ $isAr ? 'استعرض الخدمات' : 'Explore services' }}
                </a>
            </div>
        </aside>
        <div class="advantages-cards-pro">
            @foreach($advantagesItems as $index => $item)
                @php
                    $advIcon = $item['icon'] ?? 'bi-patch-check';
                    $advNote = $advantagesNotes[$index % count($advantagesNotes)];
                @endphp
                <article class="advantage-card-pro">
                    <div class="advantage-card-top">
                        <div>
                            <span class="advantage-eyebrow">{{ $isAr ? 'ميزة تشغيلية' : 'Operational edge' }}</span>
                            <h4 class="advantage-title-pro mt-3">{{ $isAr ? ($item['title_ar'] ?? '') : ($item['title_en'] ?? ($item['title_ar'] ?? '')) }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="advantage-icon-pro"><i class="bi {{ $advIcon }}"></i></span>
                            <span class="advantage-index mt-3">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                    <p class="advantage-text-pro">{{ $isAr ? ($item['description_ar'] ?? '') : ($item['description_en'] ?? ($item['description_ar'] ?? '')) }}</p>
                    <div class="advantage-footer">
                        <span class="advantage-note">{{ $isAr ? $advNote['ar'] : $advNote['en'] }}</span>
                        <span class="advantage-arrow"><i class="bi bi-arrow-up-left"></i></span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="section-shell section-shell-soft">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-person-heart"></i> {{ $isAr ? 'كوادر متخصصة' : 'Specialist team' }}</span>
            <h3>{{ $isAr ? 'فريقنا الطبي' : 'Our Doctors' }}</h3>
            <p class="section-summary">{{ $isAr ? 'بطاقات الأطباء أصبحت أوضح في عرض الصورة والتخصص والخبرة والميزة التنافسية لكل طبيب.' : 'Doctor cards now highlight specialty, experience, and value with a stronger editorial layout.' }}</p>
        </div>
        <div class="section-headline-actions">
            <a href="{{ route('front.doctors.index', app()->getLocale()) }}" class="btn btn-outline-primary">{{ $isAr ? 'كل الأطباء' : 'All doctors' }}</a>
        </div>
    </div>
    @if($featuredDoctors->isEmpty())
        <p class="text-secondary">{{ $isAr ? 'لا يوجد أطباء مميزون حاليًا.' : 'No featured doctors at the moment.' }}</p>
    @endif

    @if($featuredDoctors->isNotEmpty())
        <div class="doctors-grid-pro">
            @foreach($featuredDoctors as $d)
                @php
                    $doctorImage = $resolveMediaUrl($d->photo) ?: 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80';
                    $doctorBio = $isAr ? ($d->bio_ar ?? '') : ($d->bio_en ?: ($d->bio_ar ?? ''));
                @endphp
                <article class="doctor-pro-card">
                    <div class="doctor-photo-wrap">
                        <img class="doctor-photo" src="{{ $doctorImage }}" alt="{{ $d->name }}">
                        <span class="doctor-badge">{{ $isAr ? 'استشاري' : 'Consultant' }}</span>
                        <div class="doctor-overlay">
                            <i class="bi bi-shield-check"></i>
                            {{ $isAr ? 'رعاية دقيقة ومعايير أمان عالية' : 'Precision care with high safety standards' }}
                        </div>
                    </div>
                    <div class="doctor-pro-body">
                        <div class="doctor-meta-strip">
                            <span class="doctor-feature"><i class="bi bi-heart-pulse"></i> {{ $isAr ? 'متابعة دقيقة' : 'Close follow-up' }}</span>
                            <span>{{ $isAr ? 'متاح للحجز' : 'Available to book' }}</span>
                        </div>
                        <h6 class="doctor-name">{{ $d->name }}</h6>
                        <p class="doctor-specialty">{{ $d->specialty }}</p>
                        <div class="doctor-exp"><i class="bi bi-award"></i> {{ $d->years_experience }} {{ $isAr ? 'سنوات خبرة' : 'Years of experience' }}</div>
                        <p class="doctor-bio">{{ \Illuminate\Support\Str::limit(strip_tags((string) $doctorBio), 78) }}</p>
                        <div class="doctor-actions">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('front.doctors.show', [app()->getLocale(), $d->id]) }}">{{ $isAr ? 'عرض الملف' : 'Profile' }}</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('front.appointments.create', app()->getLocale()) }}">{{ $isAr ? 'احجز' : 'Book' }}</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</section>

<section class="section-shell testimonials-pro">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-chat-quote"></i> {{ $isAr ? 'تجارب حقيقية' : 'Real stories' }}</span>
            <h3>{{ $isAr ? 'آراء المرضى' : 'Testimonials' }}</h3>
            <p class="section-summary">{{ $isAr ? 'عرض أكثر احترافية لتجارب المرضى مع إبراز التقييم والثقة والانطباع العام عن الخدمة.' : 'A more editorial testimonial layout with visible trust signals and cleaner reading flow.' }}</p>
        </div>
    </div>
    <div class="testimonials-grid-pro">
        @foreach($testimonials as $t)
            @php
                $testimonialImage = $resolveMediaUrl($t->image ?? null) ?: 'https://ui-avatars.com/api/?name=' . urlencode($t->name) . '&background=E6EEF8&color=1E4E87';
                $rating = max(0, min(5, (int) $t->rating));
            @endphp
            <article class="testimonial-card">
                <div class="testimonial-top">
                    <div class="testimonial-author">
                        <img class="testimonial-avatar" src="{{ $testimonialImage }}" alt="{{ $t->name }}">
                        <div>
                            <p class="testimonial-name">{{ $t->name }}</p>
                            <span class="testimonial-label">{{ $isAr ? 'مريض موثق' : 'Verified patient' }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <div class="testimonial-stars" aria-label="rating {{ $rating }}/5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $rating ? 'on' : 'off' }}">★</span>
                            @endfor
                        </div>
                        <span class="testimonial-rating-badge"><i class="bi bi-patch-check-fill"></i> {{ $rating }}/5</span>
                    </div>
                </div>
                <p class="testimonial-text">{{ $isAr ? $t->comment_ar : ($t->comment_en ?: $t->comment_ar) }}</p>
                <div class="testimonial-footer">
                    <span>{{ $isAr ? 'تقييم موثّق' : 'Verified review' }}</span>
                    <span class="chip">{{ $isAr ? 'جودة الخدمة' : 'Service quality' }}</span>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="section-shell section-shell-soft">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-journal-text"></i> {{ $isAr ? 'محتوى تثقيفي' : 'Educational content' }}</span>
            <h3>{{ $isAr ? 'أحدث المقالات' : 'Latest Articles' }}</h3>
            <p class="section-summary">{{ $isAr ? 'بطاقات مقالات أكثر أناقة مع مساحة أفضل للصورة والعنوان والمقتطف وزر القراءة.' : 'A more polished article layout with better image balance, stronger titles, and cleaner read actions.' }}</p>
        </div>
        <a href="{{ route('front.blog.index', app()->getLocale()) }}" class="btn btn-outline-primary">{{ $isAr ? 'المزيد من المقالات' : 'More articles' }}</a>
    </div>
    <div class="post-grid-pro">
        @foreach($latestPosts as $post)
            @php
                $postImage = $resolveMediaUrl($post->image ?? null) ?: 'https://images.unsplash.com/photo-1588776814546-ec7e77b77d7e?auto=format&fit=crop&w=1200&q=80';
                $postTitle = $isAr ? $post->title_ar : ($post->title_en ?: $post->title_ar);
                $postContent = strip_tags($isAr ? $post->content_ar : ($post->content_en ?: $post->content_ar));
                $postDate = optional($post->published_at ?? $post->created_at)?->format('Y-m-d');
            @endphp
            <article class="post-card">
                <img class="post-thumb" src="{{ $postImage }}" alt="{{ $postTitle }}">
                <div class="post-body">
                    <span class="post-date-chip"><i class="bi bi-calendar3"></i> {{ $postDate }}</span>
                    <h4 class="post-title">{{ \Illuminate\Support\Str::limit($postTitle, 68) }}</h4>
                    <p class="post-excerpt">{{ \Illuminate\Support\Str::limit($postContent, 130) }}</p>
                    <a class="btn btn-outline-primary btn-sm post-read-btn mt-1" href="{{ route('front.blog.show', [app()->getLocale(), $post->slug]) }}">
                        {{ $isAr ? 'قراءة المقال' : 'Read post' }}
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="section-shell">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-geo-alt"></i> {{ $isAr ? 'الوصول والدوام' : 'Locations & schedule' }}</span>
            <h3>{{ $isAr ? 'الفروع وساعات العمل' : 'Branches & Working Hours' }}</h3>
            <p class="section-summary">{{ $isAr ? 'واجهة أفضل لعرض الفروع والخريطة ومواعيد العمل بطريقة أوضح وأسهل لاتخاذ قرار الحجز.' : 'A cleaner branch and map experience with clearer weekly hours and easier direction discovery.' }}</p>
        </div>
    </div>
    @php
        $openDaysCount = $hours->where('is_open', true)->count();
        $closedDaysCount = $hours->where('is_open', false)->count();
        $firstBranchPoint = $branchMapPoints->first();
    @endphp
    <div class="branches-hours-wrap">
    <div class="branches-intro-grid">
        <div class="branches-hero-card">
            <div class="branches-hero-top">
                <span class="branches-hero-badge"><i class="bi bi-compass"></i> {{ $isAr ? 'وصول أسرع للفروع' : 'Faster branch discovery' }}</span>
                <span class="branches-hero-badge"><i class="bi bi-clock-history"></i> {{ $isAr ? 'مواعيد واضحة' : 'Clear opening hours' }}</span>
            </div>
            <h4 class="branches-hero-title">{{ $isAr ? 'اختر الفرع الأقرب وشاهد المواعيد والاتجاهات من نفس المكان.' : 'Find the nearest branch, opening hours, and directions in one place.' }}</h4>
            <p class="branches-hero-copy">{{ $isAr ? 'جمعنا الخريطة، قائمة الفروع، وساعات العمل الأسبوعية داخل لوحة واحدة لتسهيل قرار الحجز والوصول بدون بحث إضافي.' : 'Map, branch list, and weekly opening hours are combined into one clearer panel to shorten the booking decision and make navigation easier.' }}</p>
            <div class="branches-hero-pills">
                <span><i class="bi bi-geo-alt"></i> {{ $isAr ? 'مواقع موثقة' : 'Verified locations' }}</span>
                <span><i class="bi bi-telephone"></i> {{ $isAr ? 'تواصل مباشر' : 'Direct contact' }}</span>
                <span><i class="bi bi-sign-turn-right"></i> {{ $isAr ? 'اتجاهات فورية' : 'Instant directions' }}</span>
            </div>
        </div>
        <div class="branches-quick-card">
            <h6>{{ $isAr ? 'نقطة انطلاق سريعة' : 'Quick starting point' }}</h6>
            <p>{{ $isAr ? 'ابدأ من قائمة الفروع أسفل الخريطة، أو اضغط على أي دبوس لعرض بيانات الفرع وروابط الاتجاهات مباشرة.' : 'Start from the branch list below the map or tap any marker to reveal branch details and direct directions.' }}</p>
            @if($firstBranchPoint)
                <div class="branch-map-actions mt-3">
                    <span class="branch-mini-chip"><i class="bi bi-geo-alt-fill"></i> {{ $firstBranchPoint['name'] }}</span>
                    @if($firstBranchPoint['maps_url'])
                        <a class="branch-map-link" href="{{ $firstBranchPoint['maps_url'] }}" target="_blank">
                            <i class="bi bi-box-arrow-up-right"></i>
                            {{ $isAr ? 'فتح الاتجاهات' : 'Open directions' }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="branches-top-stats">
        <div class="branch-stat">
            <div class="branch-stat-num">{{ $branches->count() }}</div>
            <div class="branch-stat-label">{{ $isAr ? 'فروع متاحة' : 'Active branches' }}</div>
        </div>
        <div class="branch-stat">
            <div class="branch-stat-num">{{ $openDaysCount }}</div>
            <div class="branch-stat-label">{{ $isAr ? 'أيام عمل أسبوعيًا' : 'Open days weekly' }}</div>
        </div>
        <div class="branch-stat">
            <div class="branch-stat-num">{{ $closedDaysCount }}</div>
            <div class="branch-stat-label">{{ $isAr ? 'أيام راحة' : 'Closed days' }}</div>
        </div>
    </div>
    <div class="branches-layout-pro">
        <div>
            <div class="branch-panel-card">
                <div class="branch-map-header mb-3">
                    <div>
                        <h6 class="mb-1">{{ $isAr ? 'مواعيد العمل الأسبوعية' : 'Weekly Working Hours' }}</h6>
                        <div class="branch-map-subtitle">{{ $isAr ? 'صورة أوضح لحالة كل يوم وساعات التشغيل الفعلية.' : 'A clearer snapshot of each day status and operating hours.' }}</div>
                    </div>
                </div>
                @foreach($hours as $hour)
                    <div class="hour-row-pro">
                        <div class="branch-row-head">
                            <span class="branch-row-day">{{ $isAr ? $hour->day_label_ar : $hour->day_label_en }}</span>
                            @if($hour->is_open)
                                <span class="branch-row-time">{{ substr($hour->open_at,0,5) . ' - ' . substr($hour->close_at,0,5) }}</span>
                            @endif
                        </div>
                        @if($hour->is_open)
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                <span class="hour-badge-open">{{ $isAr ? 'مفتوح' : 'Open' }}</span>
                                <span class="branch-row-note">{{ $isAr ? 'استقبال وحجوزات خلال فترة العمل' : 'Appointments and reception during working hours' }}</span>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                <span class="hour-badge-closed">{{ $isAr ? 'مغلق' : 'Closed' }}</span>
                                <span class="branch-row-note">{{ $isAr ? 'لا توجد مواعيد تشغيل في هذا اليوم' : 'No clinic operating hours on this day' }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div>
            <div class="branch-panel-card">
                <div class="branch-map-wrap">
                    <div class="branch-map-header">
                        <div>
                            <h6 class="mb-1">{{ $isAr ? 'خريطة الفروع' : 'Branches Map' }}</h6>
                            <div class="branch-map-subtitle">{{ $isAr ? 'اضغط على الدبوس أو اختر فرعًا من البطاقات لعرض التفاصيل' : 'Tap a pin or choose a branch card to view details' }}</div>
                        </div>
                        <span class="branch-mini-chip"><i class="bi bi-map"></i> {{ $branches->count() }} {{ $isAr ? 'مواقع' : 'Locations' }}</span>
                    </div>
                    <div id="branchesMap" class="branch-map"></div>
                    <div class="branch-map-list">
                        @foreach($branchMapPoints as $point)
                            <article
                                class="branch-map-item"
                                data-branch-card="1"
                                data-lat="{{ $point['lat'] }}"
                                data-lng="{{ $point['lng'] }}"
                                data-id="{{ $point['id'] }}"
                            >
                                <div class="branch-map-topline">
                                    <div class="branch-map-titlegroup">
                                        <h6><i class="bi bi-geo-alt-fill text-primary"></i> {{ $point['name'] }}</h6>
                                        <span class="branch-map-address">{{ $point['address'] }}</span>
                                    </div>
                                    <span class="branch-mini-chip">{{ $isAr ? 'فرع' : 'Branch' }}</span>
                                </div>
                                <div class="branch-map-meta">
                                    <div class="branch-meta-box">
                                        <i class="bi bi-telephone"></i>
                                        <div>
                                            <small>{{ $isAr ? 'الهاتف' : 'Phone' }}</small>
                                            <strong>{{ $point['phone'] ?: ($isAr ? 'غير متوفر' : 'Not available') }}</strong>
                                        </div>
                                    </div>
                                    <div class="branch-meta-box">
                                        <i class="bi bi-sign-turn-right"></i>
                                        <div>
                                            <small>{{ $isAr ? 'الملاحة' : 'Navigation' }}</small>
                                            <strong>{{ $isAr ? 'اتجاهات مباشرة عبر الخريطة' : 'Direct map directions' }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="branch-map-actions">
                                    @if($point['phone'])
                                        <a class="branch-action-btn" href="tel:{{ preg_replace('/\s+/', '', $point['phone']) }}">
                                            <i class="bi bi-telephone-outbound"></i>
                                            {{ $isAr ? 'هاتف مباشر' : 'Call now' }}
                                        </a>
                                    @endif
                                    @if($point['maps_url'])
                                        <a class="branch-action-btn branch-action-btn-primary" href="{{ $point['maps_url'] }}" target="_blank">
                                            <i class="bi bi-sign-turn-right"></i>
                                            {{ $isAr ? 'عرض الاتجاهات' : 'Open directions' }}
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function () {
        const mapElement = document.getElementById('branchesMap');
        if (!mapElement || typeof L === 'undefined') return;

        const points = @json($validMapPoints->all());
        const center = @json($mapCenter);

        const map = L.map('branchesMap', {
            scrollWheelZoom: false
        }).setView([center.lat, center.lng], points.length > 1 ? 11 : 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const bounds = [];
        const markersById = {};
        points.forEach(function (point) {
            const marker = L.marker([point.lat, point.lng]).addTo(map);
            const popup = `
                <div style="min-width:220px">
                    <strong style="display:block;margin-bottom:4px;">${point.name}</strong>
                    <span style="display:block;margin-bottom:4px;color:#4b647f;">${point.address || ''}</span>
                    ${point.phone ? `<a href="tel:${String(point.phone).replace(/[^0-9+]/g, '')}" style="display:block;margin-bottom:4px;">${point.phone}</a>` : ''}
                    ${point.maps_url ? `<a href="${point.maps_url}" target="_blank" rel="noopener">{{ $isAr ? 'الاتجاهات عبر Google Maps' : 'Directions on Google Maps' }}</a>` : ''}
                </div>
            `;
            marker.bindPopup(popup);
            markersById[String(point.id)] = marker;
            bounds.push([point.lat, point.lng]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [28, 28] });
        }

        const cards = Array.from(document.querySelectorAll('[data-branch-card="1"]'));
        const setActive = (activeCard) => {
            cards.forEach((card) => card.classList.toggle('active', card === activeCard));
        };

        cards.forEach((card, index) => {
            card.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const marker = markersById[id];
                const lat = Number(this.getAttribute('data-lat'));
                const lng = Number(this.getAttribute('data-lng'));

                setActive(this);

                if (!Number.isNaN(lat) && !Number.isNaN(lng)) {
                    map.setView([lat, lng], Math.max(map.getZoom(), 13), { animate: true });
                }
                if (marker) {
                    marker.openPopup();
                }
            });

            if (index === 0) {
                card.click();
            }
        });
    })();
</script>

<section class="section-shell section-shell-soft">
    <div class="section-headline-pro">
        <div>
            <span class="section-chipline"><i class="bi bi-patch-question"></i> {{ $isAr ? 'مساعدة سريعة' : 'Quick help' }}</span>
            <h3>{{ $isAr ? 'الأسئلة الشائعة' : 'FAQ' }}</h3>
            <p class="section-summary">{{ $isAr ? 'صياغة أنظف للأسئلة الشائعة مع تركيز أعلى على سهولة القراءة والطمأنة قبل الحجز.' : 'A cleaner FAQ block focused on readability, reassurance, and reducing friction before booking.' }}</p>
        </div>
    </div>
    <div class="faq-pro-wrap">
        <div class="accordion" id="faqAcc">
            @foreach($faqs as $f)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="h{{ $f->id }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c{{ $f->id }}">
                            {{ $isAr ? $f->question_ar : $f->question_en }}
                        </button>
                    </h2>
                    <div id="c{{ $f->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                        <div class="accordion-body">{{ $isAr ? $f->answer_ar : $f->answer_en }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="cta-pro-card">
        <span class="section-chipline mb-3"><i class="bi bi-lightning-charge"></i> {{ $isAr ? 'خطوة أخيرة' : 'Final step' }}</span>
        <h4 class="cta-pro-title">{{ $isAr ? 'احجز زيارتك الآن' : 'Book your visit now' }}</h4>
        <p class="cta-pro-text">{{ $isAr ? 'احجز موعدك في دقائق، واختر الفرع والخدمة المناسبة لحالتك، أو تواصل مع الفريق إذا كنت تحتاج استشارة سريعة قبل الحجز.' : 'Book in minutes, choose the right branch and treatment, or contact the team if you need quick guidance before booking.' }}</p>
        <div class="cta-pro-actions">
            <a class="btn btn-primary cta-pro-btn" href="{{ route('front.appointments.create', app()->getLocale()) }}">{{ $isAr ? 'احجز موعد' : 'Book Appointment' }}</a>
            <a class="btn btn-outline-primary cta-pro-btn" href="{{ route('front.contact.index', app()->getLocale()) }}">{{ $isAr ? 'تواصل معنا' : 'Contact Us' }}</a>
        </div>
    </div>
</section>
@endsection
