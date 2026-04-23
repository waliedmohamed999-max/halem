@extends('layouts.front')

@section('content')
@php $isAr = app()->getLocale() === 'ar'; @endphp

<style>
    .jobs-grid-modern { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .job-card-modern { padding:1.05rem; min-height:100%; display:flex; flex-direction:column; gap:.75rem; }
    .job-card-modern h3 { color:#12375f; font-size:1.1rem; font-weight:800; margin-bottom:.4rem; }
    .job-card-modern p { color:#607994; line-height:1.8; }
    @media (max-width: 991.98px) { .jobs-grid-modern { grid-template-columns:1fr 1fr; } }
    @media (max-width: 767.98px) { .jobs-grid-modern { grid-template-columns:1fr; } }
</style>

<div class="front-page">
    <section class="page-shell">
        <div class="page-hero-modern">
            <div>
                <span class="page-kicker"><i class="bi bi-briefcase"></i> {{ $isAr ? 'فرص مهنية' : 'Career opportunities' }}</span>
                <h1 class="page-title">{{ $isAr ? 'انضم إلى فريق يعمل بمعايير طبية احترافية' : 'Join a team working with a modern clinical standard' }}</h1>
                <p class="page-copy">{{ $isAr ? 'استعرض الوظائف المفتوحة، صفِّ النتائج، ثم قدّم مباشرة من نفس الصفحة داخل واجهة أوضح وأسهل في الاستخدام.' : 'Browse open positions, filter results, and apply directly from the same page in a cleaner hiring experience.' }}</p>
            </div>
            <div class="page-actions">
                <span class="meta-pill"><i class="bi bi-briefcase-fill"></i> {{ $positions->total() }} {{ $isAr ? 'وظيفة' : 'Positions' }}</span>
            </div>
        </div>
    </section>
    <section class="page-shell">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ $isAr ? 'ابحث عن وظيفة' : 'Search jobs' }}</label>
                <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="{{ $isAr ? 'اسم الوظيفة أو وصف مختصر' : 'Title or keyword' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ $isAr ? 'القسم' : 'Department' }}</label>
                <select class="form-select" name="department">
                    <option value="">{{ $isAr ? 'الكل' : 'All' }}</option>
                    @foreach($departments as $department)
                        <option value="{{ $department }}" @selected(request('department') == $department)>{{ $department }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">{{ $isAr ? 'نوع الدوام' : 'Job type' }}</label>
                <select class="form-select" name="job_type">
                    <option value="">{{ $isAr ? 'الكل' : 'All' }}</option>
                    <option value="full_time" @selected(request('job_type') === 'full_time')>{{ $isAr ? 'دوام كامل' : 'Full-time' }}</option>
                    <option value="part_time" @selected(request('job_type') === 'part_time')>{{ $isAr ? 'دوام جزئي' : 'Part-time' }}</option>
                    <option value="internship" @selected(request('job_type') === 'internship')>{{ $isAr ? 'تدريب' : 'Internship' }}</option>
                    <option value="contract" @selected(request('job_type') === 'contract')>{{ $isAr ? 'تعاقد' : 'Contract' }}</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <label class="form-label d-none d-md-block">&nbsp;</label>
                <button class="btn btn-primary">{{ $isAr ? 'تصفية' : 'Filter' }}</button>
            </div>
        </form>
    </section>
    <section class="page-shell">
        <div class="jobs-grid-modern">
            @forelse($positions as $position)
                @php
                    $jobTypeLabel = match($position->job_type) {
                        'part_time' => $isAr ? 'دوام جزئي' : 'Part-time',
                        'internship' => $isAr ? 'تدريب' : 'Internship',
                        'contract' => $isAr ? 'تعاقد' : 'Contract',
                        default => $isAr ? 'دوام كامل' : 'Full-time',
                    };
                @endphp
                <article class="surface-card job-card-modern">
                    <span class="meta-pill">{{ $jobTypeLabel }}</span>
                    <h3>{{ $position->title }}</h3>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        @if($position->department)
                            <span class="meta-pill"><i class="bi bi-diagram-3"></i> {{ $position->department }}</span>
                        @endif
                        @if($position->location)
                            <span class="meta-pill"><i class="bi bi-geo-alt"></i> {{ $position->location }}</span>
                        @endif
                    </div>
                    <p>{{ \Illuminate\Support\Str::limit($position->summary, 130) }}</p>
                    <button class="btn btn-outline-primary mt-auto" type="button" data-position-id="{{ $position->id }}">{{ $isAr ? 'قدّم الآن' : 'Apply now' }}</button>
                </article>
            @empty
                <div class="alert alert-info mb-0">{{ $isAr ? 'لا توجد وظائف مطابقة حاليًا.' : 'No matching positions at the moment.' }}</div>
            @endforelse
        </div>
        <div class="mt-4">{{ $positions->links() }}</div>
    </section>
    <section class="page-shell">
        <div class="surface-card p-4">
            <h2 class="surface-section-title mb-3">{{ $isAr ? 'تقديم سريع' : 'Quick Application' }}</h2>
            <form method="POST" action="{{ route('front.careers.store', app()->getLocale()) }}" enctype="multipart/form-data" class="row g-3" id="careerApplyForm">
                @csrf
                <div class="col-md-4"><label class="form-label">{{ $isAr ? 'الاسم الكامل' : 'Full Name' }}</label><input class="form-control" name="full_name" value="{{ old('full_name') }}" required></div>
                <div class="col-md-4"><label class="form-label">{{ $isAr ? 'رقم الهاتف' : 'Phone' }}</label><input class="form-control" name="phone" value="{{ old('phone') }}" required></div>
                <div class="col-md-4"><label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label><input class="form-control" name="email" type="email" value="{{ old('email') }}"></div>
                <div class="col-md-4">
                    <label class="form-label">{{ $isAr ? 'الوظيفة' : 'Position' }}</label>
                    <select class="form-select" name="career_position_id" id="career_position_id">
                        <option value="">{{ $isAr ? 'اختر وظيفة' : 'Select position' }}</option>
                        @foreach($allPositions as $position)
                            <option value="{{ $position->id }}" @selected(old('career_position_id') == $position->id)>{{ $position->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">{{ $isAr ? 'المدينة' : 'City' }}</label><input class="form-control" name="city" value="{{ old('city') }}"></div>
                <div class="col-md-4"><label class="form-label">{{ $isAr ? 'سنوات الخبرة' : 'Years of experience' }}</label><input class="form-control" name="experience_years" value="{{ old('experience_years') }}"></div>
                <div class="col-md-8"><label class="form-label">{{ $isAr ? 'نبذة عنك' : 'Cover Letter' }}</label><textarea class="form-control" name="cover_letter" rows="3">{{ old('cover_letter') }}</textarea></div>
                <div class="col-md-4"><label class="form-label">{{ $isAr ? 'رفع السيرة الذاتية (PDF/DOC)' : 'Upload CV (PDF/DOC)' }}</label><input class="form-control" type="file" name="cv_file" accept=".pdf,.doc,.docx"></div>
                <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary px-4">{{ $isAr ? 'إرسال طلب التقديم' : 'Submit Application' }}</button></div>
            </form>
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('[data-position-id]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const select = document.getElementById('career_position_id');
            if (!select) return;
            select.value = this.getAttribute('data-position-id');
            document.getElementById('careerApplyForm')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
</script>
@endsection
