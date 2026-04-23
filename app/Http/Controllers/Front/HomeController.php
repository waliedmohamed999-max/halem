<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Branch;
use App\Models\Doctor;
use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\WorkingHour;

class HomeController extends Controller
{
    public function index()
    {
        $sections = HomeSection::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('front.home', [
            'sections' => $sections,
            'featuredServices' => Service::query()->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->take(6)->get(),
            'featuredDoctors' => Doctor::query()->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->take(6)->get(),
            'testimonials' => Testimonial::query()->where('is_active', true)->orderBy('sort_order')->take(6)->get(),
            'faqs' => Faq::query()->where('is_active', true)->orderBy('sort_order')->take(8)->get(),
            'latestPosts' => BlogPost::query()->where('status', 'published')->latest('published_at')->take(3)->get(),
            'branches' => Branch::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'hours' => WorkingHour::query()->whereNull('branch_id')->orderBy('day_of_week')->get(),
        ]);
    }
}
