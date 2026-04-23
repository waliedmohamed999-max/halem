@extends('layouts.front')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; @endphp

<style>
    .branch-grid-modern { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
    .branch-card-modern { padding:1.05rem; min-height:100%; display:flex; flex-direction:column; }
    .branch-card-modern h3 { margin:0 0 .35rem; color:#12375f; font-size:1.15rem; font-weight:800; }
    .branch-card-modern p { margin:0 0 .8rem; color:#607994; line-height:1.8; }
    @media (max-width: 767.98px) { .branch-grid-modern { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-geo-alt"></i> {{ $isAr ? 'الفروع والمواقع' : 'Branches and locations' }}</span>
                <h1 class="page-title">{{ $isAr ? 'اختر أقرب فرع وتعرّف على العنوان ووسائل الوصول' : 'Choose the nearest branch and review address and access details' }}</h1>
                <p class="page-copy">{{ $isAr ? 'بطاقات أوضح للفروع مع روابط سريعة للتفاصيل والخريطة حتى يصل المريض بسهولة إلى المكان المناسب.' : 'A cleaner branch listing with quick links to branch details and maps so visitors can find the right location faster.' }}</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.contact.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'تواصل معنا' : 'Contact us' }}</a>
            </div>
        </div>
    </section>
    <section class="page-shell">
        <div class="branch-grid-modern">
            @foreach($branches as $branch)
                <article class="surface-card branch-card-modern">
                    <span class="meta-pill">{{ $isAr ? 'فرع' : 'Branch' }}</span>
                    <h3 class="mt-3">{{ $branch->name }}</h3>
                    <p>{{ $branch->address }}</p>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @if($branch->phone)
                            <span class="meta-pill"><i class="bi bi-telephone"></i> {{ $branch->phone }}</span>
                        @endif
                    </div>
                    <div class="d-flex gap-2 flex-wrap mt-auto">
                        <a href="{{ route('front.branches.show', [app()->getLocale(), $branch->id]) }}" class="btn btn-outline-primary">{{ $isAr ? 'تفاصيل الفرع' : 'Branch details' }}</a>
                        @if($branch->google_maps_url)
                            <a href="{{ $branch->google_maps_url }}" target="_blank" class="btn btn-primary">{{ $isAr ? 'فتح الخريطة' : 'Open map' }}</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
