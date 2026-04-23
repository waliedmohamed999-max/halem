<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $category = BlogCategory::query()->first();

        $contentAr1 = <<<'HTML'
<h2>لماذا تعتبر العناية اليومية بالأسنان مهمة؟</h2>
<p>العناية اليومية بالأسنان ليست مجرد إجراء تجميلي، بل خطوة أساسية للحفاظ على صحة الفم والجسم بشكل عام. تراكم البكتيريا في الفم قد يؤدي إلى التهابات مزمنة في اللثة، ومع الوقت قد يؤثر على جودة المضغ والنوم والثقة بالنفس.</p>
<p>الروتين البسيط المنتظم أفضل من العلاجات المكلفة لاحقًا. كل دقيقة تقضيها في العناية اليومية توفر عليك وقتًا وتكاليف كبيرة مستقبلاً.</p>
<h2>روتين يومي احترافي في 5 خطوات</h2>
<ul>
<li>تنظيف الأسنان مرتين يوميًا لمدة دقيقتين على الأقل.</li>
<li>استخدام الخيط الطبي مرة واحدة يوميًا قبل النوم.</li>
<li>غسول فم مناسب لحالتك بعد استشارة الطبيب.</li>
<li>شرب الماء بعد الوجبات للتقليل من الأحماض.</li>
<li>تقليل السكريات اللاصقة بين الوجبات.</li>
</ul>
<h2>أخطاء شائعة يجب تجنبها</h2>
<p>من أكثر الأخطاء انتشارًا: التفريش العنيف، استخدام فرشاة قاسية، وإهمال تنظيف اللسان. هذه العادات قد تسبب حساسية اللثة وتراجعها بمرور الوقت.</p>
<p>كذلك فإن تغيير الفرشاة كل 3 أشهر ضروري للحفاظ على كفاءة التنظيف ومنع تراكم البكتيريا.</p>
<h2>متى تزور الطبيب؟</h2>
<p>يفضل إجراء فحص دوري كل 6 أشهر حتى بدون ألم. أما عند وجود نزيف متكرر، ألم مستمر، أو رائحة فم لا تتحسن؛ فيجب حجز موعد مبكرًا.</p>
HTML;

        $contentEn1 = <<<'HTML'
<h2>Why Daily Oral Care Matters</h2>
<p>Daily oral care is not just cosmetic. It is a preventive health routine that protects your gums, teeth, and overall well-being. Bacterial plaque can gradually cause gum inflammation and long-term discomfort.</p>
<p>Consistent simple habits are always better than expensive corrective treatment later.</p>
<h2>A Professional 5-Step Routine</h2>
<ul>
<li>Brush twice a day for at least two minutes.</li>
<li>Floss once daily before bedtime.</li>
<li>Use a suitable mouthwash after dentist guidance.</li>
<li>Drink water after meals to reduce acidity.</li>
<li>Limit sticky sugars between meals.</li>
</ul>
<h2>Common Mistakes to Avoid</h2>
<p>Aggressive brushing, hard-bristle brushes, and skipping tongue cleaning are common issues. These habits can trigger gum sensitivity and recession over time.</p>
<p>Replacing your toothbrush every 3 months is essential for effective cleaning.</p>
<h2>When to Visit the Dentist</h2>
<p>Schedule a routine checkup every 6 months. If you have repeated bleeding, persistent pain, or lasting bad breath, book an earlier visit.</p>
HTML;

        $contentAr2 = <<<'HTML'
<h2>علامات تحتاج معها لتنظيف جير الأسنان</h2>
<p>تنظيف الجير إجراء وقائي مهم للحفاظ على اللثة والأسنان. إذا لاحظت نزيفًا بسيطًا أثناء التفريش، أو اصفرارًا قريبًا من خط اللثة، فهذه علامات مبكرة تشير إلى تراكم الجير.</p>
<h2>ما الذي يحدث إذا تم إهمال الجير؟</h2>
<p>إهمال الجير لفترة طويلة قد يسبب التهاب اللثة، انحسارها، ورائحة فم مزعجة. في مراحل متقدمة قد يحدث فقدان تدريجي في دعم الأسنان.</p>
<p>التدخل المبكر يحافظ على سلامة الأنسجة المحيطة ويمنع المضاعفات.</p>
<h2>كيف يتم التنظيف داخل العيادة؟</h2>
<ul>
<li>فحص شامل للثة وحالة التراكمات.</li>
<li>إزالة الجير بأدوات فوق صوتية آمنة.</li>
<li>تلميع الأسنان لتقليل التصبغات السطحية.</li>
<li>وضع خطة متابعة منزلية مخصصة.</li>
</ul>
<h2>نصيحة بعد الجلسة</h2>
<p>قد تشعر بحساسية خفيفة لساعات بسيطة، وهي طبيعية. التزم بتعليمات الطبيب، وتجنب المشروبات الشديدة البرودة أو السخونة مباشرة بعد الجلسة.</p>
HTML;

        $contentEn2 = <<<'HTML'
<h2>Signs You Need Professional Scaling</h2>
<p>Scaling is a preventive procedure to protect gum and tooth health. Early warning signs include minor bleeding during brushing and yellow deposits near the gumline.</p>
<h2>What Happens If Tartar Is Ignored?</h2>
<p>Long-term tartar buildup may lead to gum inflammation, recession, and persistent bad breath. Advanced cases can affect the supporting structures of teeth.</p>
<p>Early intervention helps avoid progression and maintains oral stability.</p>
<h2>What to Expect During a Scaling Session</h2>
<ul>
<li>Comprehensive gum and plaque assessment.</li>
<li>Safe ultrasonic tartar removal.</li>
<li>Polishing to reduce superficial stains.</li>
<li>Personalized home-care plan.</li>
</ul>
<h2>Aftercare Tip</h2>
<p>Mild temporary sensitivity may occur for a short period. Follow your dentist instructions and avoid extreme temperatures right after the session.</p>
HTML;

        $contentAr3 = <<<'HTML'
<h2>حشو تجميلي أم حشو تقليدي؟</h2>
<p>اختيار نوع الحشو يعتمد على موضع السن، حجم التسوس، واحتياج المريض الجمالي والوظيفي. الحشو التجميلي مناسب جدًا للأسنان الأمامية لأنه يندمج مع لون الأسنان الطبيعي.</p>
<h2>مميزات الحشو التجميلي</h2>
<ul>
<li>لون قريب جدًا من السن الطبيعي.</li>
<li>حل مناسب للتسوسات الصغيرة والمتوسطة.</li>
<li>مظهر جمالي أفضل في الابتسامة.</li>
</ul>
<h2>متى يكون الحشو التقليدي أفضل؟</h2>
<p>في بعض الحالات الخلفية التي تتعرض لضغط مضغ مرتفع، قد يوصي الطبيب بخيارات أخرى أكثر ملاءمة لطبيعة السن ودرجة التآكل.</p>
<h2>كيف تختار بشكل صحيح؟</h2>
<p>الفحص السريري والأشعة يحددان الخيار الأفضل. القرار النهائي يجب أن يكون مبنيًا على تقييم طبي دقيق وليس الشكل فقط.</p>
<p>اسأل طبيبك عن العمر الافتراضي لكل خيار وخطة المتابعة المطلوبة.</p>
HTML;

        $contentEn3 = <<<'HTML'
<h2>Composite or Traditional Filling?</h2>
<p>The right filling choice depends on tooth location, cavity size, and aesthetic expectations. Composite fillings are usually preferred for visible front teeth due to their natural look.</p>
<h2>Composite Filling Advantages</h2>
<ul>
<li>Natural tooth-like shade.</li>
<li>Suitable for small to medium cavities.</li>
<li>Better smile appearance.</li>
</ul>
<h2>When Is a Traditional Option Better?</h2>
<p>Some posterior teeth under high chewing pressure may require alternatives that better match functional demands and wear resistance.</p>
<h2>How to Decide Correctly</h2>
<p>Clinical examination and x-rays determine the best choice. The final decision should rely on professional assessment, not appearance alone.</p>
<p>Ask your dentist about durability and recommended follow-up for each option.</p>
HTML;

        $rows = [
            [
                'title_ar' => '5 نصائح للحفاظ على صحة الأسنان',
                'title_en' => '5 Tips for Better Oral Health',
                'image' => 'https://images.unsplash.com/photo-1609840114035-3c981b782dfe?auto=format&fit=crop&w=1400&q=80',
                'content_ar' => $contentAr1,
                'content_en' => $contentEn1,
                'published_at' => Carbon::now()->subDays(2),
            ],
            [
                'title_ar' => 'متى تحتاج إلى تنظيف جير الأسنان؟',
                'title_en' => 'When Do You Need Scaling?',
                'image' => 'https://images.unsplash.com/photo-1629909615184-74f495363b67?auto=format&fit=crop&w=1400&q=80',
                'content_ar' => $contentAr2,
                'content_en' => $contentEn2,
                'published_at' => Carbon::now()->subDays(5),
            ],
            [
                'title_ar' => 'الفرق بين الحشو التجميلي والحشو العادي',
                'title_en' => 'Composite vs Traditional Fillings',
                'image' => 'https://images.unsplash.com/photo-1606811971618-4486d14f3f99?auto=format&fit=crop&w=1400&q=80',
                'content_ar' => $contentAr3,
                'content_en' => $contentEn3,
                'published_at' => Carbon::now()->subDays(8),
            ],
            [
                'title_ar' => 'كيف تتعامل مع ألم الضرس المفاجئ قبل الذهاب للطبيب؟',
                'title_en' => 'How to Handle Sudden Tooth Pain Before Your Visit',
                'image' => 'https://images.unsplash.com/photo-1588776814546-ec7e77b77d7e?auto=format&fit=crop&w=1400&q=80',
                'content_ar' => '<h2>الإسعافات الأولية المنزلية</h2><p>اشطف فمك بماء فاتر وتجنب الضغط على السن المؤلم. يمكن استخدام مسكن مناسب وفق الإرشادات الطبية.</p><h2>ما يجب تجنبه</h2><ul><li>عدم وضع أسبرين مباشرة على اللثة.</li><li>تجنب الأطعمة القاسية أو شديدة البرودة.</li><li>لا تؤخر الحجز إذا استمر الألم.</li></ul><h2>متى تعتبر الحالة طارئة؟</h2><p>وجود تورم واضح، حرارة، أو ألم نابض مستمر يستدعي مراجعة عاجلة.</p>',
                'content_en' => '<h2>Home First-Aid Steps</h2><p>Rinse with warm water and avoid pressure on the painful tooth. Use suitable pain relief as medically advised.</p><h2>What to Avoid</h2><ul><li>Do not place aspirin directly on gum tissue.</li><li>Avoid hard foods or extreme temperatures.</li><li>Do not delay your dental appointment.</li></ul><h2>When Is It an Emergency?</h2><p>Visible swelling, fever, or persistent throbbing pain requires urgent evaluation.</p>',
                'published_at' => Carbon::now()->subDays(11),
            ],
            [
                'title_ar' => 'علامات مبكرة لمشاكل اللثة لا يجب تجاهلها',
                'title_en' => 'Early Gum Problem Signs You Shouldn\'t Ignore',
                'image' => 'https://images.unsplash.com/photo-1600170311833-c2cf5280ce49?auto=format&fit=crop&w=1400&q=80',
                'content_ar' => '<h2>أعراض أولية شائعة</h2><p>نزيف اللثة المتكرر، الاحمرار، والانتفاخ من أهم المؤشرات المبكرة.</p><h2>عوامل ترفع احتمالية الالتهاب</h2><ul><li>ضعف تنظيف الأسنان بين الوجبات.</li><li>التدخين.</li><li>إهمال الفحص الدوري.</li></ul><h2>خطة الوقاية</h2><p>تنظيف دوري، متابعة كل 6 أشهر، والالتزام بتعليمات الطبيب يحافظ على ثبات الأسنان.</p>',
                'content_en' => '<h2>Common Early Symptoms</h2><p>Repeated gum bleeding, redness, and puffiness are key warning signs.</p><h2>Risk Factors</h2><ul><li>Poor daily oral hygiene.</li><li>Smoking.</li><li>Skipping regular checkups.</li></ul><h2>Prevention Plan</h2><p>Professional cleaning, 6-month checkups, and home care consistency protect long-term gum stability.</p>',
                'published_at' => Carbon::now()->subDays(14),
            ],
        ];

        foreach ($rows as $row) {
            BlogPost::query()->updateOrCreate(
                ['slug' => Str::slug($row['title_en'])],
                [
                    'blog_category_id' => $category?->id,
                    'title_ar' => $row['title_ar'],
                    'title_en' => $row['title_en'],
                    'content_ar' => $row['content_ar'],
                    'content_en' => $row['content_en'],
                    'image' => $row['image'],
                    'status' => 'published',
                    'published_at' => $row['published_at'],
                    'seo_title' => $row['title_en'],
                    'meta_description' => Str::limit(strip_tags($row['content_en']), 155),
                ]
            );
        }
    }
}