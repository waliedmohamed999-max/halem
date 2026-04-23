<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->where('is_active', true)->orderBy('sort_order')->paginate(12);

        $advantagesSection = HomeSection::query()->where('section_key', 'services_advantages')->first();
        $advantagesPayload = $advantagesSection?->payload ?? [];
        $advantages = $advantagesPayload['items'] ?? [];

        return view('front.services.index', compact('services', 'advantagesSection', 'advantages'));
    }

    public function show(string $slug)
    {
        $service = Service::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('front.services.show', compact('service'));
    }
}