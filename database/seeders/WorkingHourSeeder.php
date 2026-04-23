<?php

namespace Database\Seeders;

use App\Models\WorkingHour;
use Illuminate\Database\Seeder;

class WorkingHourSeeder extends Seeder
{
    public function run(): void
    {
        $days = [
            ['id' => 0, 'ar' => 'الأحد', 'en' => 'Sunday'],
            ['id' => 1, 'ar' => 'الاثنين', 'en' => 'Monday'],
            ['id' => 2, 'ar' => 'الثلاثاء', 'en' => 'Tuesday'],
            ['id' => 3, 'ar' => 'الأربعاء', 'en' => 'Wednesday'],
            ['id' => 4, 'ar' => 'الخميس', 'en' => 'Thursday'],
            ['id' => 5, 'ar' => 'الجمعة', 'en' => 'Friday'],
            ['id' => 6, 'ar' => 'السبت', 'en' => 'Saturday'],
        ];

        foreach ($days as $day) {
            WorkingHour::query()->updateOrCreate(
                ['branch_id' => null, 'day_of_week' => $day['id']],
                [
                    'day_label_ar' => $day['ar'],
                    'day_label_en' => $day['en'],
                    'is_open' => $day['id'] !== 5,
                    'open_at' => $day['id'] !== 5 ? '10:00:00' : null,
                    'close_at' => $day['id'] !== 5 ? '22:00:00' : null,
                    'is_emergency' => false,
                ]
            );
        }
    }
}
