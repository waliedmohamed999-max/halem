<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        BlogCategory::query()->updateOrCreate(
            ['slug' => 'dental-tips'],
            ['name_ar' => 'نصائح الأسنان', 'name_en' => 'Dental Tips']
        );
    }
}
