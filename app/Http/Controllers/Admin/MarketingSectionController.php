<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSection;
use App\Models\Service;
use Illuminate\Http\Request;

class MarketingSectionController extends Controller
{
    public function edit()
    {
        $promo = $this->getSection('promo_banners', [
            'title_ar' => 'بنرات إعلانية',
            'title_en' => 'Promo Banners',
            'sort_order' => 2,
        ]);

        $offers = $this->getSection('limited_offers', [
            'title_ar' => 'العروض',
            'title_en' => 'Offers',
            'sort_order' => 3,
        ]);

        $advantages = $this->getSection('services_advantages', [
            'title_ar' => 'مميزات متفردة',
            'title_en' => 'Why Choose Us',
            'sort_order' => 4,
        ]);

        $serviceHighlights = $this->getSection('services_highlights', [
            'title_ar' => 'الخدمات المميزة',
            'title_en' => 'Featured Services',
            'sort_order' => 5,
        ]);

        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'slug', 'title_ar', 'title_en']);

        return view('admin.home-sections.marketing', compact('promo', 'offers', 'advantages', 'serviceHighlights', 'services'));
    }

    public function update(Request $request)
    {
        $promoSection = $this->getSection('promo_banners', [
            'title_ar' => 'بنرات إعلانية',
            'title_en' => 'Promo Banners',
            'sort_order' => 2,
        ]);

        $offersSection = $this->getSection('limited_offers', [
            'title_ar' => 'العروض',
            'title_en' => 'Offers',
            'sort_order' => 3,
        ]);

        $data = $request->validate([
            'promo.title_ar' => ['nullable', 'string', 'max:255'],
            'promo.title_en' => ['nullable', 'string', 'max:255'],
            'promo.sort_order' => ['nullable', 'integer', 'min:0'],
            'promo.is_active' => ['nullable', 'boolean'],
            'promo.items' => ['nullable', 'array', 'max:10'],
            'promo.items.*.badge_ar' => ['nullable', 'string', 'max:120'],
            'promo.items.*.badge_en' => ['nullable', 'string', 'max:120'],
            'promo.items.*.title_ar' => ['nullable', 'string', 'max:255'],
            'promo.items.*.title_en' => ['nullable', 'string', 'max:255'],
            'promo.items.*.subtitle_ar' => ['nullable', 'string', 'max:2000'],
            'promo.items.*.subtitle_en' => ['nullable', 'string', 'max:2000'],
            'promo.items.*.phone' => ['nullable', 'string', 'max:30'],
            'promo.items.*.bg_image' => ['nullable', 'string', 'max:2048'],
            'promo.item_images' => ['nullable', 'array'],
            'promo.item_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],

            'offers.title_ar' => ['nullable', 'string', 'max:255'],
            'offers.title_en' => ['nullable', 'string', 'max:255'],
            'offers.sort_order' => ['nullable', 'integer', 'min:0'],
            'offers.is_active' => ['nullable', 'boolean'],
            'offers.items' => ['nullable', 'array', 'max:20'],
            'offers.items.*.title_ar' => ['nullable', 'string', 'max:255'],
            'offers.items.*.title_en' => ['nullable', 'string', 'max:255'],
            'offers.items.*.discount_ar' => ['nullable', 'string', 'max:120'],
            'offers.items.*.discount_en' => ['nullable', 'string', 'max:120'],
            'offers.items.*.price' => ['nullable', 'string', 'max:40'],
            'offers.items.*.currency' => ['nullable', 'string', 'max:20'],
            'offers.items.*.image' => ['nullable', 'string', 'max:2048'],
            'offers.item_images' => ['nullable', 'array'],
            'offers.item_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],

            'advantages.title_ar' => ['nullable', 'string', 'max:255'],
            'advantages.title_en' => ['nullable', 'string', 'max:255'],
            'advantages.sort_order' => ['nullable', 'integer', 'min:0'],
            'advantages.is_active' => ['nullable', 'boolean'],
            'advantages.items' => ['nullable', 'array', 'max:20'],
            'advantages.items.*.icon' => ['nullable', 'string', 'max:80'],
            'advantages.items.*.title_ar' => ['nullable', 'string', 'max:255'],
            'advantages.items.*.title_en' => ['nullable', 'string', 'max:255'],
            'advantages.items.*.description_ar' => ['nullable', 'string', 'max:2000'],
            'advantages.items.*.description_en' => ['nullable', 'string', 'max:2000'],

            'service_icons.title_ar' => ['nullable', 'string', 'max:255'],
            'service_icons.title_en' => ['nullable', 'string', 'max:255'],
            'service_icons.sort_order' => ['nullable', 'integer', 'min:0'],
            'service_icons.is_active' => ['nullable', 'boolean'],
            'service_icons.icons' => ['nullable', 'array'],
            'service_icons.icons.*' => ['nullable', 'string', 'max:80'],
        ]);

        $promoExistingItems = $promoSection->payload['items'] ?? [];
        $this->saveSection('promo_banners', $data['promo'] ?? [], function (array $items) use ($request, $promoExistingItems): array {
            return collect($items)
                ->map(function (array $item, int $index) use ($request, $promoExistingItems): array {
                    $uploadedPath = null;
                    if ($request->hasFile("promo.item_images.$index")) {
                        $storedPath = $request->file("promo.item_images.$index")->store('marketing/promo-banners', 'public');
                        $uploadedPath = '/storage/' . ltrim($storedPath, '/');
                    }

                    $fallbackImage = trim((string) ($promoExistingItems[$index]['bg_image'] ?? ''));
                    $manualImage = trim((string) ($item['bg_image'] ?? ''));

                    return [
                        'badge_ar' => trim((string) ($item['badge_ar'] ?? '')),
                        'badge_en' => trim((string) ($item['badge_en'] ?? '')),
                        'title_ar' => trim((string) ($item['title_ar'] ?? '')),
                        'title_en' => trim((string) ($item['title_en'] ?? '')),
                        'subtitle_ar' => trim((string) ($item['subtitle_ar'] ?? '')),
                        'subtitle_en' => trim((string) ($item['subtitle_en'] ?? '')),
                        'phone' => trim((string) ($item['phone'] ?? '')),
                        'bg_image' => $uploadedPath ?: ($manualImage !== '' ? $manualImage : $fallbackImage),
                    ];
                })
                ->filter(static fn (array $item): bool => $item['title_ar'] !== '' || $item['title_en'] !== '')
                ->values()
                ->all();
        });

        $offerExistingItems = $offersSection->payload['items'] ?? [];
        $this->saveSection('limited_offers', $data['offers'] ?? [], function (array $items) use ($request, $offerExistingItems): array {
            return collect($items)
                ->map(function (array $item, int $index) use ($request, $offerExistingItems): array {
                    $uploadedPath = null;
                    if ($request->hasFile("offers.item_images.$index")) {
                        $storedPath = $request->file("offers.item_images.$index")->store('marketing/offers', 'public');
                        $uploadedPath = '/storage/' . ltrim($storedPath, '/');
                    }

                    $fallbackImage = trim((string) ($offerExistingItems[$index]['image'] ?? ''));
                    $manualImage = trim((string) ($item['image'] ?? ''));

                    return [
                        'title_ar' => trim((string) ($item['title_ar'] ?? '')),
                        'title_en' => trim((string) ($item['title_en'] ?? '')),
                        'discount_ar' => trim((string) ($item['discount_ar'] ?? '')),
                        'discount_en' => trim((string) ($item['discount_en'] ?? '')),
                        'price' => trim((string) ($item['price'] ?? '')),
                        'currency' => trim((string) ($item['currency'] ?? 'EGP')),
                        'image' => $uploadedPath ?: ($manualImage !== '' ? $manualImage : $fallbackImage),
                    ];
                })
                ->filter(static fn (array $item): bool => $item['title_ar'] !== '' || $item['title_en'] !== '')
                ->values()
                ->all();
        });

        $this->saveSection('services_advantages', $data['advantages'] ?? [], static function (array $items): array {
            return collect($items)
                ->map(static function (array $item): array {
                    return [
                        'icon' => trim((string) ($item['icon'] ?? 'bi-patch-check')),
                        'title_ar' => trim((string) ($item['title_ar'] ?? '')),
                        'title_en' => trim((string) ($item['title_en'] ?? '')),
                        'description_ar' => trim((string) ($item['description_ar'] ?? '')),
                        'description_en' => trim((string) ($item['description_en'] ?? '')),
                    ];
                })
                ->filter(static fn (array $item): bool => $item['title_ar'] !== '' || $item['title_en'] !== '')
                ->values()
                ->all();
        });

        $this->saveServiceIconsSection($data['service_icons'] ?? []);

        return redirect()
            ->route('admin.marketing-sections.edit', app()->getLocale())
            ->with('success', 'Updated successfully');
    }

    private function getSection(string $key, array $defaults): HomeSection
    {
        return HomeSection::query()->firstOrCreate(
            ['section_key' => $key],
            [
                'title_ar' => $defaults['title_ar'] ?? null,
                'title_en' => $defaults['title_en'] ?? null,
                'payload' => ['items' => []],
                'is_active' => true,
                'sort_order' => $defaults['sort_order'] ?? 0,
            ]
        );
    }

    private function saveSection(string $key, array $sectionData, callable $itemMapper): void
    {
        $items = $itemMapper($sectionData['items'] ?? []);

        HomeSection::query()->updateOrCreate(
            ['section_key' => $key],
            [
                'title_ar' => $sectionData['title_ar'] ?? null,
                'title_en' => $sectionData['title_en'] ?? null,
                'payload' => ['items' => $items],
                'is_active' => isset($sectionData['is_active']) ? (bool) $sectionData['is_active'] : false,
                'sort_order' => (int) ($sectionData['sort_order'] ?? 0),
            ]
        );
    }

    private function saveServiceIconsSection(array $sectionData): void
    {
        $icons = collect($sectionData['icons'] ?? [])
            ->mapWithKeys(static function ($icon, $serviceKey): array {
                $iconValue = trim((string) $icon);
                return [$serviceKey => $iconValue];
            })
            ->filter(static fn (string $icon): bool => $icon !== '')
            ->all();

        HomeSection::query()->updateOrCreate(
            ['section_key' => 'services_highlights'],
            [
                'title_ar' => $sectionData['title_ar'] ?? 'الخدمات المميزة',
                'title_en' => $sectionData['title_en'] ?? 'Featured Services',
                'payload' => ['icons' => $icons],
                'is_active' => isset($sectionData['is_active']) ? (bool) $sectionData['is_active'] : true,
                'sort_order' => (int) ($sectionData['sort_order'] ?? 5),
            ]
        );
    }
}