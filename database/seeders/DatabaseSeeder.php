<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SettingsSeeder::class,
            BranchSeeder::class,
            ServiceSeeder::class,
            BlogCategorySeeder::class,
            DoctorSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
            BlogPostSeeder::class,
            PageSeeder::class,
            CareerPositionSeeder::class,
            WorkingHourSeeder::class,
            HomeSectionSeeder::class,
        ]);
    }
}
