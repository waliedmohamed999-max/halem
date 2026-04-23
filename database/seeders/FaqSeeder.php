<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['q_ar' => 'هل يمكن الحجز أونلاين؟', 'q_en' => 'Can I book online?', 'a_ar' => 'نعم، يمكنك الحجز من خلال نموذج الحجز بالموقع.', 'a_en' => 'Yes, you can book directly from the website form.'],
            ['q_ar' => 'ما هي مواعيد العمل؟', 'q_en' => 'What are your working hours?', 'a_ar' => 'نعمل يوميًا من 10 صباحًا حتى 10 مساءً.', 'a_en' => 'We are open daily from 10 AM to 10 PM.'],
            ['q_ar' => 'هل تقدمون خدمات الطوارئ؟', 'q_en' => 'Do you provide emergency care?', 'a_ar' => 'نعم، يوجد خدمة طوارئ حسب التوفر.', 'a_en' => 'Yes, emergency care is available based on schedule.'],
        ];

        foreach ($rows as $index => $row) {
            Faq::query()->updateOrCreate(
                ['question_en' => $row['q_en']],
                [
                    'question_ar' => $row['q_ar'],
                    'answer_ar' => $row['a_ar'],
                    'answer_en' => $row['a_en'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
