<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['title_ar' => 'تنظيف الأسنان', 'title_en' => 'Teeth Cleaning'],
            ['title_ar' => 'حشو الأسنان', 'title_en' => 'Dental Fillings'],
            ['title_ar' => 'زراعة الأسنان', 'title_en' => 'Dental Implants'],
            ['title_ar' => 'تركيبات الأسنان', 'title_en' => 'Dental Crowns'],
            ['title_ar' => 'تبييض الأسنان', 'title_en' => 'Teeth Whitening'],
        ];

        foreach ($services as $index => $service) {
            Service::query()->updateOrCreate(
                ['slug' => Str::slug($service['title_en'])],
                [
                    'title_ar' => $service['title_ar'],
                    'title_en' => $service['title_en'],
                    'description_ar' => 'خدمة احترافية باستخدام أحدث الأجهزة الطبية.',
                    'description_en' => 'Professional service using modern dental technology.',
                    'full_content_ar' => 'تفاصيل كاملة عن الخدمة وإجراءاتها قبل وبعد العلاج.',
                    'full_content_en' => 'Full details about the service and treatment workflow.',
                    'is_active' => true,
                    'is_featured' => $index < 3,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
