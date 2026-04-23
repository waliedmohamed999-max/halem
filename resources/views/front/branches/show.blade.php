@extends('layouts.front')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; @endphp

<style>
    .branch-doctors-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    @media (max-width: 991.98px) { .branch-doctors-grid { grid-template-columns:1fr 1fr; } }
    @media (max-width: 767.98px) { .branch-doctors-grid { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-geo-alt-fill"></i> {{ $isAr ? 'تفاصيل الفرع' : 'Branch details' }}</span>
                <h1 class="page-title">{{ $branch->name }}</h1>
                <p class="page-copy">{{ $branch->address }}</p>
                <div class="d-flex gap-2 flex-wrap mt-3">
                    @if($branch->phone)
                        <span class="meta-pill"><i class="bi bi-telephone"></i> {{ $branch->phone }}</span>
                    @endif
                    @if($branch->google_maps_url)
                        <span class="meta-pill"><i class="bi bi-map"></i> {{ $isAr ? 'متاح على الخريطة' : 'Available on map' }}</span>
                    @endif
                </div>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.branches.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'العودة للفروع' : 'Back to branches' }}</a>
                @if($branch->google_maps_url)
                    <a href="{{ $branch->google_maps_url }}" target="_blank" class="btn btn-primary px-4">{{ $isAr ? 'فتح الخريطة' : 'Open map' }}</a>
                @endif
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="split-layout">
            <div class="surface-card p-4">
                <h2 class="surface-section-title mb-3">{{ $isAr ? 'ساعات العمل' : 'Working Hours' }}</h2>
                @forelse($branch->workingHours as $hour)
                    <div class="work-hour-row mb-2">
                        <span>{{ $isAr ? $hour->day_label_ar : $hour->day_label_en }}</span>
                        <span>{{ $hour->is_open ? (substr((string) $hour->open_at,0,5) . ' - ' . substr((string) $hour->close_at,0,5)) : ($isAr ? 'مغلق' : 'Closed') }}</span>
                    </div>
                @empty
                    <p class="text-secondary mb-0">{{ $isAr ? 'لا توجد ساعات مخصصة لهذا الفرع حاليًا.' : 'No working hours are assigned to this branch yet.' }}</p>
                @endforelse
            </div>

            <aside class="surface-card p-4">
                <h3 class="surface-section-title mb-3">{{ $isAr ? 'إجراءات سريعة' : 'Quick actions' }}</h3>
                <div class="d-grid gap-2">
                    @if($branch->phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $branch->phone) }}" class="btn btn-outline-primary">{{ $isAr ? 'اتصال مباشر' : 'Direct call' }}</a>
                    @endif
                    <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز في هذا الفرع' : 'Book in this branch' }}</a>
                </div>
            </aside>
        </div>
    </section>

    @if($branch->doctors->count())
        <section class="page-shell">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h2 class="surface-section-title mb-0">{{ $isAr ? 'الأطباء في هذا الفرع' : 'Doctors in this branch' }}</h2>
            </div>
            <div class="branch-doctors-grid">
                @foreach($branch->doctors as $doctor)
                    <article class="surface-card p-4">
                        <span class="meta-pill"><i class="bi bi-person"></i> {{ $isAr ? 'طبيب' : 'Doctor' }}</span>
                        <h3 class="mt-3" style="color:#12375f; font-size:1.15rem; font-weight:800;">{{ $doctor->name }}</h3>
                        <p class="mb-3" style="color:#607994;">{{ $doctor->specialty }}</p>
                        <a href="{{ route('front.doctors.show', [app()->getLocale(), $doctor->id]) }}" class="btn btn-outline-primary">{{ $isAr ? 'عرض الملف' : 'View profile' }}</a>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
