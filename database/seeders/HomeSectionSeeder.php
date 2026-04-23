<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'section_key' => 'hero',
                'title_ar' => 'الرئيسية',
                'title_en' => 'Home Hero',
                'payload' => [
                    'title_ar' => 'عيادات د. حليم',
                    'title_en' => 'Dr. Halim Dental Clinics',
                    'text_ar' => 'رعاية متكاملة للأسنان بأحدث التقنيات وعلى يد أطباء متخصصين.',
                    'text_en' => 'Complete dental care using modern technology and expert doctors.',
                ],
                'sort_order' => 1,
            ],
            [
                'section_key' => 'promo_banners',
                'title_ar' => 'بنرات إعلانية',
                'title_en' => 'Promo Banners',
                'payload' => [
                    'items' => [
                        [
                            'badge_ar' => 'إعلان خاص',
                            'badge_en' => 'Special Ad',
                            'title_ar' => 'ابتسامة صحية تبدأ بخطة علاج دقيقة',
                            'title_en' => 'A healthy smile starts with a precise plan',
                            'subtitle_ar' => 'فحص رقمي شامل وخطة علاج مناسبة لكل حالة مع متابعة مستمرة.',
                            'subtitle_en' => 'Digital checkup and personalized treatment plan with continuous follow-up.',
                            'phone' => '01028234921',
                            'bg_image' => 'https://images.unsplash.com/photo-1629909615957-be95c2f2f6f6?auto=format&fit=crop&w=1600&q=80',
                        ],
                        [
                            'badge_ar' => 'حجز سريع',
                            'badge_en' => 'Quick Booking',
                            'title_ar' => 'عروض تجميل الأسنان لفترة محدودة',
                            'title_en' => 'Limited-time cosmetic dentistry offers',
                            'subtitle_ar' => 'احجز الآن واستفد من خصومات على التبييض والعدسات التجميلية.',
                            'subtitle_en' => 'Book now and get discounts on whitening and smile design.',
                            'phone' => '01028234921',
                            'bg_image' => 'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?auto=format&fit=crop&w=1600&q=80',
                        ],
                    ],
                ],
                'sort_order' => 2,
            ],
            [
                'section_key' => 'limited_offers',
                'title_ar' => 'العروض',
                'title_en' => 'Offers',
                'payload' => [
                    'items' => [
                        [
                            'title_ar' => 'تنظيف وتلميع الأسنان',
                            'title_en' => 'Teeth Cleaning & Polishing',
                            'discount_ar' => 'خصم 30%',
                            'discount_en' => '30% Off',
                            'price' => '450',
                            'currency' => 'EGP',
                            'image' => 'https://images.unsplash.com/photo-1588776814546-ec7e77b77d7e?auto=format&fit=crop&w=900&q=80',
                        ],
                        [
                            'title_ar' => 'تبييض الأسنان',
                            'title_en' => 'Teeth Whitening',
                            'discount_ar' => 'خصم 25%',
                            'discount_en' => '25% Off',
                            'price' => '1200',
                            'currency' => 'EGP',
                            'image' => 'https://images.unsplash.com/photo-1593022356769-11f762e25ed9?auto=format&fit=crop&w=900&q=80',
                        ],
                        [
                            'title_ar' => 'تقويم الأسنان',
                            'title_en' => 'Orthodontics',
                            'discount_ar' => 'استشارة مجانية',
                            'discount_en' => 'Free Consultation',
                            'price' => '250',
                            'currency' => 'EGP',
                            'image' => 'https://images.unsplash.com/photo-1609840112855-9f0fe8cc9a8b?auto=format&fit=crop&w=900&q=80',
                        ],
                        [
                            'title_ar' => 'حشو العصب',
                            'title_en' => 'Root Canal Treatment',
                            'discount_ar' => 'خصم 20%',
                            'discount_en' => '20% Off',
                            'price' => '900',
                            'currency' => 'EGP',
                            'image' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=900&q=80',
                        ],
                    ],
                ],
                'sort_order' => 3,
            ],
            [
                'section_key' => 'services_advantages',
                'title_ar' => 'ميزات متفردة',
                'title_en' => 'Why Choose Us',
                'payload' => [
                    'items' => [
                        [
                            'icon' => 'bi-award',
                            'title_ar' => 'الخبرة',
                            'title_en' => 'Experience',
                            'description_ar' => 'كوادر طبية متخصصة في جميع فروع طب الأسنان بخبرات عملية وتدريب مستمر لتقديم نتائج دقيقة وآمنة.',
                            'description_en' => 'Specialized dentists across all disciplines with continuous training and proven clinical experience.',
                        ],
                        [
                            'icon' => 'bi-cpu',
                            'title_ar' => 'التطور',
                            'title_en' => 'Modern Technology',
                            'description_ar' => 'نطبق أحدث الممارسات ونستخدم أجهزة متقدمة للتشخيص والعلاج لضمان أفضل النتائج وراحة المرضى.',
                            'description_en' => 'We use modern diagnostics and treatment technologies to ensure better outcomes and patient comfort.',
                        ],
                        [
                            'icon' => 'bi-shield-check',
                            'title_ar' => 'الأمان',
                            'title_en' => 'Safety',
                            'description_ar' => 'معايير تعقيم صارمة وبروتوكولات واضحة داخل العيادات لضمان أعلى مستويات الأمان والجودة.',
                            'description_en' => 'Strict sterilization standards and clear clinical protocols to maintain top safety and quality.',
                        ],
                        [
                            'icon' => 'bi-geo-alt',
                            'title_ar' => 'تميّز الموقع',
                            'title_en' => 'Prime Location',
                            'description_ar' => 'موقع مركزي يسهل الوصول إليه مع فروع متعددة لتقديم الرعاية بالقرب منك.',
                            'description_en' => 'Central, easy-to-reach locations with multiple branches near you.',
                        ],
                        [
                            'icon' => 'bi-gem',
                            'title_ar' => 'الفخامة',
                            'title_en' => 'Premium Experience',
                            'description_ar' => 'بيئة مريحة وتصميم عصري وتجربة استقبال منظمة تمنح المريض شعورًا بالثقة والاطمئنان.',
                            'description_en' => 'A modern and comfortable environment with an organized patient experience.',
                        ],
                        [
                            'icon' => 'bi-people',
                            'title_ar' => 'المسؤولية',
                            'title_en' => 'Responsibility',
                            'description_ar' => 'متابعة دقيقة لكل حالة من أول زيارة حتى اكتمال العلاج مع اهتمام حقيقي بكل التفاصيل.',
                            'description_en' => 'Close follow-up from first visit through treatment completion with real attention to detail.',
                        ],
                    ],
                ],
                'sort_order' => 4,
            ],
            [
                'section_key' => 'about_summary',
                'title_ar' => 'من نحن',
                'title_en' => 'About Summary',
                'payload' => [
                    'title_ar' => 'خبرة ورعاية وثقة',
                    'title_en' => 'Experience, Care, and Trust',
                ],
                'sort_order' => 5,
            ],
            [
                'section_key' => 'services_highlights',
                'title_ar' => 'خدماتنا',
                'title_en' => 'Services Highlights',
                'payload' => null,
                'sort_order' => 6,
            ],
            [
                'section_key' => 'doctors_highlights',
                'title_ar' => 'فريقنا الطبي',
                'title_en' => 'Doctors Highlights',
                'payload' => null,
                'sort_order' => 7,
            ],
            [
                'section_key' => 'testimonials',
                'title_ar' => 'آراء العملاء',
                'title_en' => 'Testimonials',
                'payload' => null,
                'sort_order' => 8,
            ],
            [
                'section_key' => 'faq',
                'title_ar' => 'الأسئلة الشائعة',
                'title_en' => 'FAQ',
                'payload' => null,
                'sort_order' => 9,
            ],
            [
                'section_key' => 'latest_blog',
                'title_ar' => 'أحدث المقالات',
                'title_en' => 'Latest Blog',
                'payload' => null,
                'sort_order' => 10,
            ],
            [
                'section_key' => 'contact_cta',
                'title_ar' => 'احجز الآن',
                'title_en' => 'Contact CTA',
                'payload' => [
                    'title_ar' => 'احجز موعدك الآن',
                    'title_en' => 'Book Your Appointment Now',
                ],
                'sort_order' => 11,
            ],
        ];

        foreach ($sections as $section) {
            HomeSection::query()->updateOrCreate(
                ['section_key' => $section['section_key']],
                [
                    'title_ar' => $section['title_ar'],
                    'title_en' => $section['title_en'],
                    'payload' => $section['payload'],
                    'is_active' => true,
                    'sort_order' => $section['sort_order'],
                ]
            );
        }
    }
}