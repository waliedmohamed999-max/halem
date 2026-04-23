@extends('layouts.front')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; @endphp

<style>
    .blog-grid-modern { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .blog-card-modern { overflow:hidden; min-height:100%; }
    .blog-card-modern img { width:100%; height:210px; object-fit:cover; background:#eef3fb; }
    .blog-card-modern .body { padding:1rem; display:flex; flex-direction:column; gap:.65rem; min-height:calc(100% - 210px); }
    .blog-card-modern h3 { margin:0; color:#12375f; font-size:1.15rem; font-weight:800; line-height:1.5; }
    .blog-card-modern p { margin:0; color:#617993; line-height:1.8; }
    @media (max-width: 991.98px) { .blog-grid-modern { grid-template-columns:1fr 1fr; } }
    @media (max-width: 767.98px) { .blog-grid-modern { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-journal-text"></i> {{ $isAr ? 'محتوى تثقيفي' : 'Educational content' }}</span>
                <h1 class="page-title">{{ $isAr ? 'المدونة الطبية والنصائح العملية لصحة الأسنان' : 'Dental blog with practical advice and educational content' }}</h1>
                <p class="page-copy">{{ $isAr ? 'مقالات مختصرة وواضحة تساعد الزائر على فهم الحالات الشائعة والعناية اليومية واتخاذ قرار الحجز في الوقت المناسب.' : 'Clear articles that help visitors understand common conditions, daily care, and when to book a dental visit.' }}</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary px-4">{{ $isAr ? 'احجز استشارة' : 'Book consultation' }}</a>
            </div>
        </div>
    </section>
    <section class="page-shell">
        <div class="blog-grid-modern">
            @foreach($posts as $post)
                @php
                    $title = $isAr ? $post->title_ar : $post->title_en;
                    $content = $isAr ? $post->content_ar : $post->content_en;
                    $cleanText = trim(strip_tags((string) $content));
                    $excerpt = \Illuminate\Support\Str::limit($cleanText, 140);
                    $wordCount = str_word_count($cleanText);
                    $readingMinutes = max(1, (int) ceil($wordCount / 180));
                    $image = null;
                    if (!empty($post->image)) {
                        if (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://'])) $image = $post->image;
                        elseif (\Illuminate\Support\Str::startsWith($post->image, ['storage/', '/storage/'])) $image = asset(ltrim($post->image, '/'));
                        else $image = asset('storage/' . ltrim($post->image, '/'));
                    }
                    $image = $image ?: 'https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=1200&q=80';
                @endphp
                <article class="surface-card blog-card-modern">
                    <div class="card-media-overlay">
                        <img src="{{ $image }}" alt="{{ $title }}">
                    </div>
                    <div class="body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="meta-pill"><i class="bi bi-calendar3"></i> {{ optional($post->published_at)->format('Y-m-d') }}</span>
                            <span class="meta-pill"><i class="bi bi-clock"></i> {{ $readingMinutes }} {{ $isAr ? 'دقيقة قراءة' : 'min read' }}</span>
                        </div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $excerpt }}</p>
                        <a href="{{ route('front.blog.show', [app()->getLocale(), $post->slug]) }}" class="action-link mt-auto">{{ $isAr ? 'قراءة المقال' : 'Read article' }} <i class="bi bi-arrow-up-left"></i></a>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-4">{{ $posts->links() }}</div>
    </section>
</div>
@endsection
