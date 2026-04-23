<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['name' => 'أحمد خالد', 'ar' => 'تجربة ممتازة وتعامل محترف جدًا.', 'en' => 'Excellent experience and very professional team.', 'rating' => 5],
            ['name' => 'منة الله علي', 'ar' => 'نتيجة رائعة في تجميل الأسنان وأنصح بالمركز.', 'en' => 'Great cosmetic result. Highly recommended.', 'rating' => 5],
            ['name' => 'محمد سمير', 'ar' => 'المواعيد منضبطة والدكتور شرح الخطة بوضوح.', 'en' => 'Appointments are on time and treatment plan was clear.', 'rating' => 4],
        ];

        foreach ($rows as $index => $row) {
            Testimonial::query()->updateOrCreate(
                ['name' => $row['name']],
                [
                    'comment_ar' => $row['ar'],
                    'comment_en' => $row['en'],
                    'rating' => $row['rating'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
