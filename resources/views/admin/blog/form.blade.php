@extends('layouts.admin')
@section('content')
<div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-3">
    <h4 class="mb-0">{{ isset($post) ? 'تعديل مقال' : 'إضافة مقال' }}</h4>
    <button type="button" class="btn btn-outline-primary" id="generate-article-template">
        توليد هيكل مقال احترافي
    </button>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ isset($post)?route('admin.blog-posts.update',[app()->getLocale(),$post]):route('admin.blog-posts.store',app()->getLocale()) }}">
    @csrf
    @if(isset($post))
        @method('PUT')
    @endif

    <div class="row g-2">
        <div class="col-md-4">
            <label class="form-label">التصنيف</label>
            <select class="form-select" name="blog_category_id">
                <option value="">Category</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('blog_category_id',$post->blog_category_id ?? '')==$c->id)>
                        {{ app()->getLocale() === 'ar' ? ($c->name_ar ?? $c->name_en) : $c->name_en }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">العنوان (AR)</label>
            <input class="form-control" id="post_title_ar" name="title_ar" value="{{ old('title_ar',$post->title_ar ?? '') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Title (EN)</label>
            <input class="form-control" id="post_title_en" name="title_en" value="{{ old('title_en',$post->title_en ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">المحتوى (AR)</label>
            <textarea class="form-control" id="post_content_ar" name="content_ar" rows="14">{{ old('content_ar',$post->content_ar ?? '') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Content (EN)</label>
            <textarea class="form-control" id="post_content_en" name="content_en" rows="14">{{ old('content_en',$post->content_en ?? '') }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Slug</label>
            <input class="form-control" name="slug" value="{{ old('slug',$post->slug ?? '') }}" placeholder="slug">
        </div>
        <div class="col-md-4">
            <label class="form-label">تاريخ النشر</label>
            <input type="datetime-local" class="form-control" name="published_at" value="{{ old('published_at',isset($post->published_at)?$post->published_at->format('Y-m-d\TH:i'):'') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">الحالة</label>
            <select class="form-select" name="status">
                <option value="draft" @selected(old('status',$post->status ?? 'draft')==='draft')>draft</option>
                <option value="published" @selected(old('status',$post->status ?? '')==='published')>published</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">SEO Title</label>
            <input class="form-control" id="post_seo_title" name="seo_title" value="{{ old('seo_title',$post->seo_title ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Meta Description</label>
            <textarea class="form-control" id="post_meta_description" name="meta_description" rows="2">{{ old('meta_description',$post->meta_description ?? '') }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">الصورة</label>
            <input type="file" class="form-control" name="image">
        </div>
    </div>

    <button class="btn btn-success mt-3">Save</button>
</form>

<script>
    (function () {
        const generateBtn = document.getElementById('generate-article-template');
        if (!generateBtn) return;

        const fields = {
            titleAr: document.getElementById('post_title_ar'),
            titleEn: document.getElementById('post_title_en'),
            contentAr: document.getElementById('post_content_ar'),
            contentEn: document.getElementById('post_content_en'),
            seoTitle: document.getElementById('post_seo_title'),
            metaDescription: document.getElementById('post_meta_description')
        };

        const filled = () => Object.values(fields).some((el) => el && el.value.trim() !== '');

        const arabicTemplate = {
            title: 'ابتسامة هوليود: الدليل الكامل قبل اتخاذ القرار',
            content: `<h2>مقدمة</h2>
<p>ابتسامة هوليود من أكثر الإجراءات التجميلية شيوعًا في عيادات الأسنان الحديثة، لأنها تمنح تحسينًا واضحًا في شكل الأسنان والثقة بالنفس خلال فترة قصيرة.</p>
<h2>ما هي ابتسامة هوليود؟</h2>
<p>هي إجراء تجميلي يعتمد غالبًا على القشور الخزفية (فينير) لتحسين اللون والشكل والحجم وإخفاء العيوب البسيطة مثل التصبغات والفراغات وعدم التناسق.</p>
<h2>من هو المرشح المناسب؟</h2>
<ul>
<li>من يبحث عن تحسين جمالي واضح للأسنان الأمامية.</li>
<li>من لديه تصبغات لا تستجيب للتبييض التقليدي.</li>
<li>من يعاني من فراغات صغيرة أو حواف مكسورة بشكل بسيط.</li>
</ul>
<h2>مميزات الإجراء</h2>
<ul>
<li>نتائج سريعة ومظهر طبيعي.</li>
<li>تحسين لون وتناسق الأسنان.</li>
<li>رفع الثقة بالنفس عند الابتسام والتحدث.</li>
</ul>
<h2>الخطوات داخل العيادة</h2>
<ol>
<li>فحص شامل وتصوير الحالة.</li>
<li>تخطيط الابتسامة الرقمية واختيار الشكل المناسب.</li>
<li>تحضير بسيط للأسنان وأخذ المقاسات.</li>
<li>تجربة الشكل النهائي ثم التثبيت الدائم.</li>
</ol>
<h2>نصائح بعد الإجراء</h2>
<ul>
<li>الالتزام بتنظيف الأسنان مرتين يوميًا.</li>
<li>استخدام الخيط الطبي بانتظام.</li>
<li>تجنب العادات التي قد تضر الأسنان مثل فتح الأشياء الصلبة بها.</li>
</ul>
<h2>خاتمة</h2>
<p>اختيار الطبيب المناسب وخطة العلاج الدقيقة هما العاملان الأهم لنتيجة ناجحة ومستمرة. احجز استشارتك لتقييم حالتك واختيار الحل الأمثل لابتسامتك.</p>`
        };

        const englishTemplate = {
            title: 'Hollywood Smile: The Complete Guide Before You Decide',
            content: `<h2>Introduction</h2>
<p>A Hollywood Smile is one of the most requested cosmetic dental procedures, offering fast visual improvement and greater self-confidence.</p>
<h2>What Is a Hollywood Smile?</h2>
<p>It is usually based on porcelain veneers to improve tooth color, symmetry, shape, and minor imperfections such as stains or small gaps.</p>
<h2>Who Is a Good Candidate?</h2>
<ul>
<li>Patients looking for a clear cosmetic enhancement.</li>
<li>Patients with deep stains resistant to regular whitening.</li>
<li>Patients with minor spacing or edge defects.</li>
</ul>
<h2>Main Benefits</h2>
<ul>
<li>Fast and natural-looking results.</li>
<li>Improved color and smile harmony.</li>
<li>Higher confidence in social and professional settings.</li>
</ul>
<h2>Procedure Steps</h2>
<ol>
<li>Clinical assessment and case photography.</li>
<li>Digital smile design and treatment planning.</li>
<li>Minimal preparation and impressions.</li>
<li>Try-in session and final bonding.</li>
</ol>
<h2>Aftercare Tips</h2>
<ul>
<li>Brush twice daily and floss consistently.</li>
<li>Attend periodic follow-up visits.</li>
<li>Avoid habits that can damage teeth or restorations.</li>
</ul>
<h2>Conclusion</h2>
<p>Accurate planning and proper case selection are key to long-term successful results. Book a consultation to choose the best plan for your smile.</p>`
        };

        generateBtn.addEventListener('click', function () {
            if (filled() && !confirm('سيتم استبدال النص الحالي. هل تريد المتابعة؟')) {
                return;
            }

            fields.titleAr.value = arabicTemplate.title;
            fields.titleEn.value = englishTemplate.title;
            fields.contentAr.value = arabicTemplate.content;
            fields.contentEn.value = englishTemplate.content;
            fields.seoTitle.value = 'ابتسامة هوليود | مركز د. حليم لطب الأسنان';
            fields.metaDescription.value = 'تعرف على خطوات ومميزات ابتسامة هوليود، المرشح المناسب، أهم النصائح بعد الإجراء، وكيف تختار الخطة الأنسب لحالتك.';
        });
    })();
</script>
@endsection


