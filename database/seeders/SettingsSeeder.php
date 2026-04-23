<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'مركز د. حليم لطب الأسنان',
            'site_city' => 'مصر - القاهرة',
            'site_phone' => '01028234921',
            'site_email' => 'info@drhalim-dental.com',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'whatsapp_url' => 'https://wa.me/201028234921',
            'google_maps_url' => 'https://maps.google.com',
            'hero_subtitle_ar' => 'رعاية متكاملة لأسنانك بأحدث التقنيات وعلى يد أطباء متخصصين.',
            'hero_subtitle_en' => 'Complete dental care with modern technologies and expert doctors.',
            'about_long_ar' => 'نحن مركز متخصص في طب الأسنان العلاجي والتجميلي مع اهتمام كامل براحة المريض.',
            'about_long_en' => 'We are a specialized dental center focused on quality, comfort, and modern treatment.',
            'emergency_enabled' => '1',
            'emergency_text' => 'خدمة الطوارئ متاحة للحالات العاجلة.',
            'emergency_phone' => '01028234921',
            'notifications_enabled' => '1',
            'appointment_price_regular' => '300',
            'appointment_price_vip' => '600',
            'logo' => '',
            'favicon' => '',
            'brand_mode' => 'text',
        ];

        foreach ($defaults as $key => $value) {
            Setting::setValue($key, $value);
        }
    }
}
