@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $photo = $doctor->photo
        ? (\Illuminate\Support\Str::startsWith($doctor->photo, ['http://', 'https://'])
            ? $doctor->photo
            : asset('storage/' . ltrim(str_replace('storage/', '', $doctor->photo), '/')))
        : 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=1200&q=80';
    $bio = $isAr ? ($doctor->bio_ar ?: '') : ($doctor->bio_en ?: $doctor->bio_ar);
    $expertiseRaw = $isAr ? ($doctor->expertise_ar ?? '') : ($doctor->expertise_en ?: ($doctor->expertise_ar ?? ''));
    $bookingRaw = $isAr ? ($doctor->booking_method_ar ?? '') : ($doctor->booking_method_en ?: ($doctor->booking_method_ar ?? ''));
    $expertiseItems = collect(preg_split('/\r\n|\r|\n/', (string) $expertiseRaw))->map(fn ($line) => trim($line))->filter()->values();
    $bookingSteps = collect(preg_split('/\r\n|\r|\n/', (string) $bookingRaw))->map(fn ($line) => trim($line))->filter()->values();
@endphp

<style>
    .doctor-profile-hero { display:grid; grid-template-columns:minmax(280px,.85fr) minmax(0,1.15fr); gap:1rem; align-items:stretch; }
    .doctor-profile-photo { width:100%; height:100%; min-height:340px; object-fit:cover; border-radius:1.25rem; background:#e8f1fb; }
    .doctor-info-item { padding:.85rem; }
    @media (max-width: 991.98px) { .doctor-profile-hero { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="doctor-profile-hero">
            <div class="surface-card p-3">
                <img src="{{ $photo }}" alt="{{ $doctor->name }}" class="doctor-profile-photo">
            </div>
            <div class="page-hero-modern">
                <div>
                    <span class="page-kicker"><i class="bi bi-person-badge"></i> {{ $isAr ? 'ملف الطبيب' : 'Doctor profile' }}</span>
                    <h1 class="page-title">{{ $doctor->name }}</h1>
                    <p class="page-copy">{{ $doctor->specialty }}</p>
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <span class="meta-pill"><i class="bi bi-award"></i> {{ $doctor->years_experience }} {{ $isAr ? 'سنوات خبرة' : 'Years exp.' }}</span>
                        @if($doctor->mainBranch)
                            <span class="meta-pill"><i class="bi bi-geo-alt"></i> {{ $doctor->mainBranch->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="page-actions">
                    <a href="{{ route('front.doctors.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'العودة للأطباء' : 'Back to doctors' }}</a>
                    <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary px-4">{{ $isAr ? 'احجز الآن' : 'Book now' }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="info-grid-2">
            <div class="surface-card doctor-info-item"><strong class="d-block mb-2">{{ $isAr ? 'التخصص' : 'Specialty' }}</strong><span>{{ $doctor->specialty }}</span></div>
            <div class="surface-card doctor-info-item"><strong class="d-block mb-2">{{ $isAr ? 'الخبرة' : 'Experience' }}</strong><span>{{ $doctor->years_experience }} {{ $isAr ? 'سنوات' : 'years' }}</span></div>
            <div class="surface-card doctor-info-item"><strong class="d-block mb-2">{{ $isAr ? 'الفرع الأساسي' : 'Main branch' }}</strong><span>{{ $doctor->mainBranch->name ?? '-' }}</span></div>
            <div class="surface-card doctor-info-item"><strong class="d-block mb-2">{{ $isAr ? 'أنواع الزيارات' : 'Visit types' }}</strong><span>{{ $isAr ? 'كشف / متابعة / استشارة' : 'Checkup / Follow-up / Consultation' }}</span></div>
        </div>
    </section>

    <section class="page-shell">
        <div class="split-layout">
            <article class="surface-card p-4">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'نبذة تعريفية' : 'Professional bio' }}</h2>
                <p class="mb-0 lh-lg" style="color:#35526f;">{{ $bio ?: ($isAr ? 'سيتم تحديث النبذة قريبًا.' : 'Bio will be updated soon.') }}</p>
            </article>
            <aside class="surface-card p-4">
                <h3 class="surface-section-title mb-3">{{ $isAr ? 'طريقة الحجز' : 'How to book' }}</h3>
                <ol class="mb-0 ps-3" style="line-height:1.9;color:#35526f;">
                    @forelse($bookingSteps as $step)
                        <li>{{ $step }}</li>
                    @empty
                        <li>{{ $isAr ? 'اختر الخدمة المناسبة.' : 'Choose the suitable service.' }}</li>
                        <li>{{ $isAr ? 'حدد الفرع والتاريخ والوقت.' : 'Select branch, date, and time.' }}</li>
                        <li>{{ $isAr ? 'أرسل الطلب وانتظر التأكيد.' : 'Submit the request and wait for confirmation.' }}</li>
                    @endforelse
                </ol>
            </aside>
        </div>
    </section>

    <section class="page-shell">
        <div class="surface-card p-4">
            <h2 class="surface-section-title mb-3">{{ $isAr ? 'خبرات الطبيب' : 'Doctor expertise' }}</h2>
            @if($expertiseItems->isNotEmpty())
                <ul class="mb-0 ps-3" style="line-height:1.95;color:#35526f;">
                    @foreach($expertiseItems as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <ul class="mb-0 ps-3" style="line-height:1.95;color:#35526f;">
                    <li>{{ $isAr ? 'تشخيص دقيق ووضع خطة علاج مناسبة للحالة.' : 'Accurate diagnosis and tailored treatment planning.' }}</li>
                    <li>{{ $isAr ? 'متابعة بعد الجلسات لضمان أفضل نتيجة.' : 'Post-session follow-up for better outcomes.' }}</li>
                    <li>{{ $isAr ? 'استخدام تقنيات حديثة في المجال.' : 'Using modern techniques in the specialty.' }}</li>
                </ul>
            @endif
        </div>
    </section>
</div>
@endsection
