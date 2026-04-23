<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'name_ar' => 'حلوان',
                'name_en' => 'Helwan',
                'address_ar' => 'حلوان - شارع منصور - بالقرب من محطة المترو',
                'address_en' => 'Helwan, Mansour St., near Metro Station',
                'google_maps_url' => 'https://www.google.com/maps?q=29.8413,31.3003',
            ],
            [
                'name_ar' => 'مدينة نصر',
                'name_en' => 'Nasr City',
                'address_ar' => 'مدينة نصر - شارع عباس العقاد - بجوار سيتي ستارز',
                'address_en' => 'Nasr City, Abbas El-Akkad St., next to City Stars',
                'google_maps_url' => 'https://www.google.com/maps?q=30.0729,31.3463',
            ],
            [
                'name_ar' => 'حدائق القبة',
                'name_en' => 'Hadayek El Kobba',
                'address_ar' => 'حدائق القبة - شارع مصر والسودان - أمام قسم الحدائق',
                'address_en' => 'Hadayek El Kobba, Masr & Sudan St., in front of district office',
                'google_maps_url' => 'https://www.google.com/maps?q=30.0918,31.2826',
            ],
        ];

        foreach ($rows as $index => $row) {
            Branch::query()->updateOrCreate(
                ['name_en' => $row['name_en']],
                [
                    'name_ar' => $row['name_ar'],
                    'address_ar' => $row['address_ar'],
                    'address_en' => $row['address_en'],
                    'phone' => '01028234921',
                    'google_maps_url' => $row['google_maps_url'],
                    'working_hours' => '10:00 AM - 10:00 PM',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
