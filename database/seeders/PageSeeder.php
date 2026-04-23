<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'about',
                'title_ar' => 'معلومات عنا',
                'title_en' => 'About Us',
                'content_ar' => <<<HTML
<h2>من نحن</h2>
<p>مركز د. حليم لطب الأسنان هو مركز متخصص في تقديم رعاية متكاملة لصحة الفم والأسنان، يجمع بين الخبرة الطبية، التقنيات الحديثة، والاهتمام الحقيقي براحة المريض في كل خطوة من خطوات العلاج.</p>
<p>نعمل وفق بروتوكولات واضحة تبدأ بالتشخيص الدقيق، ثم شرح الخطة العلاجية، ثم التنفيذ والمتابعة لضمان أفضل نتيجة علاجية وتجميلية ممكنة.</p>

<h2>رؤيتنا</h2>
<p>أن نكون من أفضل مراكز طب الأسنان في مصر في جودة الخدمة، دقة التشخيص، ورضا المرضى، مع بناء تجربة علاجية آمنة ومريحة لكل أفراد الأسرة.</p>

<h2>رسالتنا</h2>
<ul>
<li>تقديم علاج آمن وفعال باستخدام أحدث المعايير العلمية.</li>
<li>توفير خيارات علاج متعددة تناسب كل حالة وميزانية.</li>
<li>الالتزام بالشفافية الكاملة في شرح الخطة والتكلفة والمدة.</li>
<li>بناء علاقة طويلة المدى مع المرضى قائمة على الثقة والمتابعة.</li>
</ul>

<h2>قيمنا الأساسية</h2>
<ul>
<li><strong>الخبرة:</strong> فريق طبي متخصص في العلاجات العامة والتجميلية والجراحية.</li>
<li><strong>الأمان:</strong> تطبيق معايير تعقيم ومكافحة عدوى صارمة داخل كل غرفة.</li>
<li><strong>التطوير:</strong> تحديث مستمر للأجهزة والبروتوكولات العلاجية.</li>
<li><strong>الاهتمام:</strong> متابعة ما بعد العلاج لضمان ثبات النتائج وراحة المريض.</li>
</ul>

<h2>كيف نعمل معك؟</h2>
<ol>
<li>كشف أولي شامل مع تصوير وفحص الحالة.</li>
<li>تجهيز خطة علاج مفصلة بمراحل واضحة.</li>
<li>تنفيذ الخطة وفق جدول مريح للمريض.</li>
<li>متابعة دورية وتوجيهات وقائية للحفاظ على النتائج.</li>
</ol>
HTML,
                'content_en' => <<<HTML
<h2>Who We Are</h2>
<p>Dr. Halim Dental Center provides comprehensive oral and dental care by combining clinical expertise, modern technology, and a patient-centered approach.</p>
<p>Our workflow starts with precise diagnosis, followed by a clear treatment plan, high-quality execution, and continuous follow-up.</p>

<h2>Our Vision</h2>
<p>To be one of the leading dental centers in Egypt in terms of treatment quality, patient satisfaction, and long-term clinical outcomes.</p>

<h2>Our Mission</h2>
<ul>
<li>Deliver safe and effective treatment based on modern standards.</li>
<li>Provide personalized options suitable for each patient.</li>
<li>Maintain full transparency in treatment plans and timelines.</li>
<li>Build long-term trust through consistent follow-up care.</li>
</ul>

<h2>Core Values</h2>
<ul>
<li><strong>Expertise:</strong> Skilled doctors across general and cosmetic dentistry.</li>
<li><strong>Safety:</strong> Strict sterilization and infection-control protocols.</li>
<li><strong>Innovation:</strong> Continuous upgrades in technology and methods.</li>
<li><strong>Care:</strong> Ongoing support after treatment completion.</li>
</ul>
HTML,
                'seo_title' => 'About Us | Dr. Halim Dental',
                'meta_description' => 'Learn about Dr. Halim Dental Center, our mission, values, treatment approach, and commitment to high-quality dental care.',
            ],
            [
                'slug' => 'privacy-policy',
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'content_ar' => <<<HTML
<h2>خصوصية بياناتك</h2>
<p>نحن ملتزمون بحماية بيانات المرضى والزوار، ولا يتم استخدام أي بيانات إلا لتقديم الخدمة الطبية والتواصل المرتبط بالحجوزات.</p>
<h3>ما البيانات التي نجمعها؟</h3>
<ul>
<li>الاسم ورقم الهاتف والبريد الإلكتروني (إن وُجد).</li>
<li>بيانات الحجز والرسائل المرسلة عبر الموقع.</li>
<li>معلومات تقنية أساسية لتحسين تجربة الاستخدام.</li>
</ul>
<h3>كيف نستخدم البيانات؟</h3>
<ul>
<li>تأكيد المواعيد والرد على الاستفسارات.</li>
<li>تحسين جودة الخدمة وتجربة الموقع.</li>
<li>حماية المنصة من الاستخدام غير المشروع.</li>
</ul>
HTML,
                'content_en' => <<<HTML
<h2>Your Data Privacy</h2>
<p>We are committed to protecting patient and visitor data. Information is used only for service delivery and appointment communication.</p>
<h3>Data We Collect</h3>
<ul>
<li>Name, phone number, and optional email.</li>
<li>Appointment and contact form details.</li>
<li>Basic technical information for usability improvement.</li>
</ul>
HTML,
                'seo_title' => 'Privacy Policy | Dr. Halim Dental',
                'meta_description' => 'Read our privacy policy and how we protect patient and visitor data at Dr. Halim Dental Center.',
            ],
            [
                'slug' => 'terms',
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms & Conditions',
                'content_ar' => <<<HTML
<h2>شروط استخدام الموقع</h2>
<p>باستخدامك لهذا الموقع، فأنت توافق على الالتزام بالشروط والأحكام التالية:</p>
<ul>
<li>المعلومات الطبية المنشورة للتوعية ولا تغني عن الكشف المباشر.</li>
<li>لا يتم تأكيد الموعد النهائي إلا بعد التواصل من فريق الاستقبال.</li>
<li>يحق للمركز تعديل مواعيد العمل والخدمات عند الحاجة التشغيلية.</li>
<li>أي إساءة استخدام للنماذج أو المحتوى قد تؤدي إلى حظر الاستخدام.</li>
</ul>
HTML,
                'content_en' => <<<HTML
<h2>Website Terms</h2>
<p>By using this website, you agree to the following terms and conditions:</p>
<ul>
<li>Published medical information is for awareness and does not replace in-clinic diagnosis.</li>
<li>Appointments are considered final only after confirmation by our reception team.</li>
<li>The center may update working hours and services when operationally required.</li>
<li>Any misuse of forms or content may result in restricted access.</li>
</ul>
HTML,
                'seo_title' => 'Terms & Conditions | Dr. Halim Dental',
                'meta_description' => 'Read the terms and conditions for using Dr. Halim Dental website and online services.',
            ],
            [
                'slug' => 'careers',
                'title_ar' => 'الوظائف',
                'title_en' => 'Careers',
                'content_ar' => <<<HTML
<h2>انضم إلى فريقنا</h2>
<p>نبحث دائمًا عن الكفاءات الطبية والإدارية التي تؤمن بجودة الخدمة وراحة المريض.</p>
<h3>التخصصات المطلوبة غالبًا</h3>
<ul>
<li>أطباء أسنان (عام / تجميل / علاج جذور).</li>
<li>مساعدو أطباء أسنان.</li>
<li>موظفو استقبال وخدمة عملاء.</li>
<li>مسؤول تسويق رقمي طبي.</li>
</ul>
<p>للتقديم: أرسل السيرة الذاتية عبر صفحة التواصل أو البريد الإلكتروني الرسمي للمركز.</p>
HTML,
                'content_en' => <<<HTML
<h2>Join Our Team</h2>
<p>We are always looking for qualified clinical and administrative talent who value quality care and patient comfort.</p>
<h3>Common Openings</h3>
<ul>
<li>Dentists (General / Cosmetic / Endodontic).</li>
<li>Dental assistants.</li>
<li>Reception and patient support staff.</li>
<li>Medical digital marketing specialist.</li>
</ul>
HTML,
                'seo_title' => 'Careers | Dr. Halim Dental',
                'meta_description' => 'Explore career opportunities at Dr. Halim Dental Center and apply to join our team.',
            ],
        ];

        foreach ($rows as $row) {
            Page::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title_ar' => $row['title_ar'],
                    'title_en' => $row['title_en'],
                    'content_ar' => $row['content_ar'],
                    'content_en' => $row['content_en'],
                    'is_active' => true,
                    'seo_title' => $row['seo_title'],
                    'meta_description' => $row['meta_description'],
                ]
            );
        }
    }
}
