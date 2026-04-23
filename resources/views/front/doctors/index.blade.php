@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $resolveMediaUrl = static function (?string $path): ?string {
        if (! $path) return null;
        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) return $path;
        if (\Illuminate\Support\Str::startsWith($path, ['/storage/', 'storage/'])) return asset(ltrim($path, '/'));
        return asset('storage/' . ltrim($path, '/'));
    };
@endphp

<style>
    .doctor-grid-modern { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:1rem; }
    .doctor-card-modern { overflow:hidden; min-height:100%; }
    .doctor-card-modern img { width:100%; height:245px; object-fit:cover; background:#e9f2fb; }
    .doctor-card-modern .body { padding:1rem; }
    .doctor-card-modern h3 { margin-bottom:.2rem; color:#14365e; font-size:1.15rem; font-weight:800; }
    .doctor-card-modern p { color:#607994; margin-bottom:.65rem; }
    .doctor-meta-modern { display:flex; gap:.45rem; flex-wrap:wrap; margin-bottom:.8rem; }
    @media (max-width: 1199.98px) { .doctor-grid-modern { grid-template-columns:repeat(3,minmax(0,1fr)); } }
    @media (max-width: 991.98px) { .doctor-grid-modern { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width: 767.98px) { .doctor-grid-modern { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-person-heart"></i> {{ $isAr ? 'فريق طبي متخصص' : 'Specialist team' }}</span>
                <h1 class="page-title">{{ $isAr ? 'نخبة الأطباء في مختلف تخصصات الأسنان' : 'A curated specialist team across dental disciplines' }}</h1>
                <p class="page-copy">{{ $isAr ? 'اعرض ملفات الأطباء، سنوات الخبرة، والتخصصات الأساسية داخل واجهة أوضح تساعد المريض على اختيار الطبيب المناسب.' : 'Browse doctor profiles, years of experience, and specialties in a cleaner interface that helps patients choose confidently.' }}</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary px-4">{{ $isAr ? 'احجز موعد' : 'Book appointment' }}</a>
                <a href="{{ route('front.contact.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'تواصل معنا' : 'Contact us' }}</a>
            </div>
        </div>
    </section>
    <section class="page-shell">
        <div class="page-stats">
            <div class="page-stat"><strong>{{ $doctors->total() }}+</strong><span>{{ $isAr ? 'طبيب متخصص' : 'Specialists' }}</span></div>
            <div class="page-stat"><strong>{{ $doctors->pluck('mainBranch.id')->filter()->unique()->count() }}</strong><span>{{ $isAr ? 'فروع متاحة' : 'Active branches' }}</span></div>
            <div class="page-stat"><strong>{{ number_format((float) $doctors->avg('years_experience'), 1) }}</strong><span>{{ $isAr ? 'متوسط الخبرة' : 'Average experience' }}</span></div>
        </div>
    </section>
    <section class="page-shell">
        <div class="doctor-grid-modern">
            @forelse($doctors as $doctor)
                @php $doctorImage = $resolveMediaUrl($doctor->photo) ?: 'https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=900&q=80'; @endphp
                <article class="surface-card doctor-card-modern">
                    <div class="card-media-overlay">
                        <img src="{{ $doctorImage }}" alt="{{ $doctor->name }}">
                    </div>
                    <div class="body">
                        <span class="meta-pill">{{ $isAr ? 'متاح للحجز' : 'Available to book' }}</span>
                        <h3 class="mt-3">{{ $doctor->name }}</h3>
                        <p>{{ $doctor->specialty }}</p>
                        <div class="doctor-meta-modern">
                            <span class="meta-pill"><i class="bi bi-award"></i> {{ $doctor->years_experience }} {{ $isAr ? 'سنوات خبرة' : 'Years exp.' }}</span>
                            @if($doctor->mainBranch)
                                <span class="meta-pill"><i class="bi bi-geo-alt"></i> {{ $doctor->mainBranch->name }}</span>
                            @endif
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('front.doctors.show', [app()->getLocale(), $doctor->id]) }}" class="btn btn-outline-primary">{{ $isAr ? 'عرض الملف' : 'View profile' }}</a>
                            <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز' : 'Book' }}</a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="alert alert-info mb-0">{{ $isAr ? 'لا يوجد أطباء مضافون حاليًا.' : 'No doctors available yet.' }}</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $doctors->links() }}</div>
    </section>
</div>
@endsection
