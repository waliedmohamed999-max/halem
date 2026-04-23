<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Page;
use App\Models\Service;
use App\Models\Setting;
use App\Models\WorkingHour;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        $aboutData = null;
        if ($slug === 'about') {
            $aboutData = [
                'featuredServices' => Service::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->take(6)
                    ->get(),
                'featuredDoctors' => Doctor::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->take(4)
                    ->get(),
                'workingHours' => WorkingHour::query()
                    ->whereNull('branch_id')
                    ->orderBy('day_of_week')
                    ->get(),
                'stats' => [
                    'doctors' => Doctor::query()->where('is_active', true)->count(),
                    'services' => Service::query()->where('is_active', true)->count(),
                    'experience_years' => (int) Setting::getValue('experience_years', 12),
                    'patients' => (int) Setting::getValue('patients_count', 23000),
                ],
            ];
        }

        return view('front.pages.show', compact('page', 'aboutData'));
    }
}
