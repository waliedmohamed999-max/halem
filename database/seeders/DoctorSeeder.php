<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::query()->pluck('id', 'name_en');

        $rows = [
            [
                'name_ar' => 'د. حليم محمد',
                'name_en' => 'Dr. Halim Mohamed',
                'specialty_ar' => 'تركيبات وتجميل الأسنان',
                'specialty_en' => 'Cosmetic & Restorative Dentistry',
                'years_experience' => 10,
                'bio_ar' => 'متخصص في تصميم الابتسامة وتركيبات الأسنان الحديثة.',
                'bio_en' => 'Specialized in smile design and modern restorations.',
                'branch_en' => 'Helwan',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'د. سارة أحمد',
                'name_en' => 'Dr. Sara Ahmed',
                'specialty_ar' => 'علاج الجذور وحشو الأسنان',
                'specialty_en' => 'Endodontics & Fillings',
                'years_experience' => 8,
                'bio_ar' => 'خبرة في علاج العصب والحالات المعقدة بدون ألم.',
                'bio_en' => 'Experienced in painless root canal treatment.',
                'branch_en' => 'Nasr City',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'د. كريم علي',
                'name_en' => 'Dr. Karim Ali',
                'specialty_ar' => 'زراعة الأسنان',
                'specialty_en' => 'Dental Implants',
                'years_experience' => 12,
                'bio_ar' => 'متخصص في زراعة الأسنان والتعويضات الثابتة.',
                'bio_en' => 'Expert in implants and fixed prosthodontics.',
                'branch_en' => 'Hadayek El Kobba',
                'is_featured' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($rows as $row) {
            $doctor = Doctor::query()->updateOrCreate(
                ['name_en' => $row['name_en']],
                [
                    'name_ar' => $row['name_ar'],
                    'specialty_ar' => $row['specialty_ar'],
                    'specialty_en' => $row['specialty_en'],
                    'years_experience' => $row['years_experience'],
                    'bio_ar' => $row['bio_ar'],
                    'bio_en' => $row['bio_en'],
                    'branch_id' => $branches[$row['branch_en']] ?? null,
                    'is_active' => true,
                    'is_featured' => $row['is_featured'],
                    'sort_order' => $row['sort_order'],
                ]
            );

            if ($doctor->branch_id) {
                $doctor->branches()->syncWithoutDetaching([$doctor->branch_id]);
            }
        }
    }
}
