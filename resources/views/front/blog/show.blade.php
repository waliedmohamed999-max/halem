@extends('layouts.front')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $title = $isAr ? $post->title_ar : $post->title_en;
    $contentHtml = $isAr ? $post->content_ar : $post->content_en;
    $plainText = trim(strip_tags((string) $contentHtml));
    $wordCount = str_word_count($plainText);
    $readingMinutes = max(1, (int) ceil($wordCount / 180));
    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/iu', (string) $contentHtml, $matches);
    $headings = collect($matches[1] ?? [])->map(fn ($h) => trim(strip_tags((string) $h)))->filter()->values()->take(6)->all();
    $keyPoints = collect(preg_split('/[\.\!\?؟]+/u', $plainText))->map(fn ($line) => trim((string) $line))->filter(fn ($line) => mb_strlen($line) > 35)->take(3)->values()->all();
    $image = null;
    if (!empty($post->image)) {
        if (\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://'])) $image = $post->image;
        elseif (\Illuminate\Support\Str::startsWith($post->image, ['storage/', '/storage/'])) $image = asset(ltrim($post->image, '/'));
        else $image = asset('storage/' . ltrim($post->image, '/'));
    }
    $image = $image ?: 'https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=1600&q=80';
@endphp

<style>
    .post-cover-modern { width:100%; height:390px; object-fit:cover; border-radius:1.2rem; background:#edf3fb; }
    .article-modern { line-height:2; color:#28405a; }
    .article-modern h2, .article-modern h3 { color:#123f73; font-weight:800; margin-top:1.1rem; margin-bottom:.65rem; }
    .article-modern p, .article-modern li { font-size:1.03rem; }
    .quick-point { padding:.7rem .8rem; border:1px dashed #c6d8ea; border-radius:.8rem; background:#f8fcff; color:#425970; }
    .related-grid-modern { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .related-card-modern { padding:1rem; min-height:100%; display:flex; flex-direction:column; }
    .related-card-modern h3 { margin:0; color:#12375f; font-size:1.05rem; font-weight:800; line-height:1.5; }
    @media (max-width: 991.98px) { .post-cover-modern { height:260px; } }
    @media (max-width: 767.98px) { .related-grid-modern { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-journal-richtext"></i> {{ $isAr ? 'مقال طبي' : 'Article' }}</span>
                <h1 class="page-title">{{ $title }}</h1>
                <div class="d-flex gap-2 flex-wrap mt-3">
                    <span class="meta-pill"><i class="bi bi-calendar3"></i> {{ optional($post->published_at)->format('Y-m-d') }}</span>
                    <span class="meta-pill"><i class="bi bi-clock"></i> {{ $readingMinutes }} {{ $isAr ? 'دقيقة قراءة' : 'min read' }}</span>
                    <span class="meta-pill"><i class="bi bi-type"></i> {{ number_format($wordCount) }} {{ $isAr ? 'كلمة' : 'words' }}</span>
                </div>
            </div>
            <div class="page-actions">
                <a href="{{ route('front.blog.index', app()->getLocale()) }}" class="btn btn-outline-primary px-4">{{ $isAr ? 'العودة للمدونة' : 'Back to blog' }}</a>
                <button type="button" class="btn btn-primary px-4" onclick="navigator.clipboard.writeText(window.location.href)">{{ $isAr ? 'نسخ الرابط' : 'Copy link' }}</button>
            </div>
        </div>
    </section>

    <section class="page-shell">
        <div class="split-layout">
            <article class="surface-card p-4">
                <img class="post-cover-modern mb-4" src="{{ $image }}" alt="{{ $title }}">
                <div class="article-modern">{!! $contentHtml !!}</div>
                <div class="d-flex gap-2 flex-wrap mt-4">
                    <a href="{{ route('front.appointments.create', app()->getLocale()) }}" class="btn btn-primary">{{ $isAr ? 'احجز موعد' : 'Book appointment' }}</a>
                    <a href="{{ route('front.services.index', app()->getLocale()) }}" class="btn btn-outline-primary">{{ $isAr ? 'تصفح الخدمات' : 'Browse services' }}</a>
                </div>
            </article>
            <aside class="front-page">
                <div class="surface-card p-4">
                    <h3 class="surface-section-title mb-3">{{ $isAr ? 'ملخص سريع' : 'Quick summary' }}</h3>
                    <div class="front-page">
                        @forelse($keyPoints as $point)
                            <div class="quick-point">{{ $point }}</div>
                        @empty
                            <div class="text-secondary small">{{ $isAr ? 'سيظهر الملخص عند توفر محتوى أطول.' : 'A summary appears for longer articles.' }}</div>
                        @endforelse
                    </div>
                </div>
                <div class="surface-card p-4">
                    <h3 class="surface-section-title mb-3">{{ $isAr ? 'محاور المقال' : 'Article sections' }}</h3>
                    @if(!empty($headings))
                        <ul class="mb-0 ps-3" style="line-height:1.9;color:#35526f;">
                            @foreach($headings as $heading)
                                <li>{{ $heading }}</li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-secondary small">{{ $isAr ? 'أضف عناوين فرعية داخل المقال لتظهر هنا.' : 'Add section headings inside the article to list them here.' }}</div>
                    @endif
                </div>
                @if($relatedServices->isNotEmpty())
                    <div class="surface-card p-4">
                        <h3 class="surface-section-title mb-3">{{ $isAr ? 'خدمات مرتبطة' : 'Related services' }}</h3>
                        <div class="d-grid gap-2">
                            @foreach($relatedServices as $service)
                                <a class="btn btn-outline-primary text-start" href="{{ route('front.services.show', [app()->getLocale(), $service->slug]) }}">{{ $service->title }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </section>

    @if($relatedPosts->isNotEmpty())
        <section class="page-shell">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h2 class="surface-section-title mb-0">{{ $isAr ? 'مقالات مقترحة' : 'Related articles' }}</h2>
                <a href="{{ route('front.contact.index', app()->getLocale()) }}" class="action-link">{{ $isAr ? 'تواصل معنا' : 'Contact us' }} <i class="bi bi-arrow-up-left"></i></a>
            </div>
            <div class="related-grid-modern">
                @foreach($relatedPosts as $related)
                    <article class="surface-card related-card-modern">
                        <div class="d-flex flex-column gap-3 h-100">
                            <span class="meta-pill"><i class="bi bi-calendar3"></i> {{ optional($related->published_at)->format('Y-m-d') }}</span>
                            <h3>{{ $isAr ? $related->title_ar : $related->title_en }}</h3>
                            <a href="{{ route('front.blog.show', [app()->getLocale(), $related->slug]) }}" class="action-link mt-auto">{{ $isAr ? 'قراءة المقال' : 'Read article' }} <i class="bi bi-arrow-up-left"></i></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
