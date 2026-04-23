<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\HandlesImageUpload;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use HandlesImageUpload;

    private array $keys = [
        'site_name', 'site_city', 'site_phone', 'site_email', 'facebook_url', 'instagram_url',
        'whatsapp_url', 'google_maps_url', 'hero_subtitle_ar', 'hero_subtitle_en',
        'about_long_ar', 'about_long_en', 'emergency_enabled', 'emergency_text', 'emergency_phone',
        'notifications_enabled', 'logo', 'favicon', 'brand_mode',
        'appointment_price_regular', 'appointment_price_vip',
        'seller_vat_number', 'seller_cr_number', 'seller_address_ar', 'seller_address_en',
        'invoice_footer_note_ar', 'invoice_footer_note_en',
    ];

    public function index()
    {
        $settings = Setting::query()->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $this->save($request);

        return back()->with('success', 'Saved successfully');
    }

    public function update(Request $request, Setting $setting)
    {
        $this->save($request);

        return back()->with('success', 'Updated successfully');
    }

    private function save(Request $request): void
    {
        $data = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_city' => ['nullable', 'string', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_email' => ['nullable', 'email'],
            'facebook_url' => ['nullable', 'url'],
            'instagram_url' => ['nullable', 'url'],
            'whatsapp_url' => ['nullable', 'url'],
            'google_maps_url' => ['nullable', 'url'],
            'hero_subtitle_ar' => ['nullable', 'string'],
            'hero_subtitle_en' => ['nullable', 'string'],
            'about_long_ar' => ['nullable', 'string'],
            'about_long_en' => ['nullable', 'string'],
            'emergency_enabled' => ['nullable', 'boolean'],
            'emergency_text' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:50'],
            'notifications_enabled' => ['nullable', 'boolean'],
            'appointment_price_regular' => ['nullable', 'numeric', 'min:0'],
            'appointment_price_vip' => ['nullable', 'numeric', 'min:0'],
            'seller_vat_number' => ['nullable', 'string', 'max:150'],
            'seller_cr_number' => ['nullable', 'string', 'max:150'],
            'seller_address_ar' => ['nullable', 'string', 'max:255'],
            'seller_address_en' => ['nullable', 'string', 'max:255'],
            'invoice_footer_note_ar' => ['nullable', 'string'],
            'invoice_footer_note_en' => ['nullable', 'string'],
            // Use file+mimes instead of image to allow svg/ico reliably.
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,jpg,jpeg,webp,svg', 'max:1024'],
            'brand_mode' => ['nullable', 'in:text,logo,both'],
        ]);

        $logo = $this->storeImage($request->file('logo'), 'settings', Setting::getValue('logo'));
        $favicon = $this->storeImage($request->file('favicon'), 'settings', Setting::getValue('favicon'));

        foreach ($this->keys as $key) {
            if ($key === 'logo') {
                Setting::setValue('logo', $logo ?? (string) Setting::getValue('logo'));
                continue;
            }

            if ($key === 'favicon') {
                Setting::setValue('favicon', $favicon ?? (string) Setting::getValue('favicon'));
                continue;
            }

            $value = $data[$key] ?? null;
            if (in_array($key, ['emergency_enabled', 'notifications_enabled'], true)) {
                $value = $request->boolean($key) ? '1' : '0';
            }

            Setting::setValue($key, (string) $value);
        }
    }
}
