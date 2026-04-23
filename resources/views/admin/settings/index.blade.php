@extends('layouts.admin')

@section('content')
@php
    $isAr = app()->getLocale() === 'ar';
    $logo = $settings['logo'] ?? null;
    $favicon = $settings['favicon'] ?? null;

    $resolveMedia = static function (?string $value): ?string {
        if (! $value) return null;
        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) return $value;
        return asset('storage/' . ltrim(str_replace('storage/', '', $value), '/'));
    };
@endphp

<style>
    .settings-wrap { display: grid; gap: 1rem; }
    .settings-card {
        border: 1px solid #d7e4f2;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 20px rgba(17, 54, 96, .06);
        overflow: hidden;
    }
    .settings-card-head {
        padding: .75rem 1rem;
        border-bottom: 1px solid #e4edf6;
        background: linear-gradient(120deg, #f5f9ff 0%, #eef5ff 100%);
        font-weight: 700;
        color: #123b67;
    }
    .settings-card-body { padding: 1rem; }
    .hint { font-size: .8rem; color: #6b8098; }
    .preview-box {
        border: 1px dashed #cddff2;
        border-radius: .9rem;
        padding: .8rem;
        background: #fbfdff;
        height: 100%;
    }
</style>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h4 class="mb-0">{{ $isAr ? 'إعدادات الموقع' : 'Site Settings' }}</h4>
    <span class="badge text-bg-light border">{{ $isAr ? 'تحديث مباشر من لوحة التحكم' : 'Live update from dashboard' }}</span>
</div>

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.store', app()->getLocale()) }}">
    @csrf

    <div class="settings-wrap">
        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'البيانات العامة' : 'General Information' }}</div>
            <div class="settings-card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'اسم المركز' : 'Site Name' }}</label>
                        <input class="form-control" name="site_name" value="{{ old('site_name',$settings['site_name'] ?? 'مركز د. حليم لطب الأسنان') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'المدينة' : 'City' }}</label>
                        <input class="form-control" name="site_city" value="{{ old('site_city',$settings['site_city'] ?? 'مصر - القاهرة') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'الهاتف الرئيسي' : 'Main Phone' }}</label>
                        <input class="form-control" name="site_phone" value="{{ old('site_phone',$settings['site_phone'] ?? '01028234921') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'البريد الإلكتروني' : 'Email' }}</label>
                        <input class="form-control" name="site_email" value="{{ old('site_email',$settings['site_email'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ $isAr ? 'رابط واتساب' : 'WhatsApp URL' }}</label>
                        <input class="form-control" name="whatsapp_url" value="{{ old('whatsapp_url',$settings['whatsapp_url'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'روابط التواصل' : 'Social & Map Links' }}</div>
            <div class="settings-card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Facebook</label>
                        <input class="form-control" name="facebook_url" value="{{ old('facebook_url',$settings['facebook_url'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Instagram</label>
                        <input class="form-control" name="instagram_url" value="{{ old('instagram_url',$settings['instagram_url'] ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Google Maps</label>
                        <input class="form-control" name="google_maps_url" value="{{ old('google_maps_url',$settings['google_maps_url'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'محتوى الواجهة' : 'Homepage Content' }}</div>
            <div class="settings-card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'وصف Hero بالعربية' : 'Hero Subtitle (AR)' }}</label>
                        <textarea class="form-control" rows="2" name="hero_subtitle_ar">{{ old('hero_subtitle_ar',$settings['hero_subtitle_ar'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'Hero بالإنجليزية' : 'Hero Subtitle (EN)' }}</label>
                        <textarea class="form-control" rows="2" name="hero_subtitle_en">{{ old('hero_subtitle_en',$settings['hero_subtitle_en'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'من نحن بالعربية' : 'About Long (AR)' }}</label>
                        <textarea class="form-control" rows="3" name="about_long_ar">{{ old('about_long_ar',$settings['about_long_ar'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'من نحن بالإنجليزية' : 'About Long (EN)' }}</label>
                        <textarea class="form-control" rows="3" name="about_long_en">{{ old('about_long_en',$settings['about_long_en'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'الهوية البصرية' : 'Branding' }}</div>
            <div class="settings-card-body">
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">{{ $isAr ? 'وضع الشعار' : 'Brand Mode' }}</label>
                                <select class="form-select" name="brand_mode">
                                    <option value="text" @selected(old('brand_mode', $settings['brand_mode'] ?? 'text')==='text')>Brand: Text only</option>
                                    <option value="logo" @selected(old('brand_mode', $settings['brand_mode'] ?? 'text')==='logo')>Brand: Logo only</option>
                                    <option value="both" @selected(old('brand_mode', $settings['brand_mode'] ?? 'text')==='both')>Brand: Logo + Text</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Logo</label>
                                <input type="file" class="form-control" name="logo">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Favicon</label>
                                <input type="file" class="form-control" name="favicon">
                            </div>
                            <div class="col-12"><span class="hint">{{ $isAr ? 'يُفضّل PNG أو SVG بخلفية شفافة للشعار.' : 'PNG or SVG with transparent background is recommended.' }}</span></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="preview-box">
                            <div class="mb-2 fw-semibold">{{ $isAr ? 'معاينة سريعة' : 'Quick Preview' }}</div>
                            @if($logo)
                                <img src="{{ $resolveMedia($logo) }}" class="img-thumbnail mb-2" style="max-height:80px;" alt="logo">
                            @endif
                            @if($favicon)
                                <img src="{{ $resolveMedia($favicon) }}" class="img-thumbnail" style="max-height:48px;" alt="favicon">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'إعدادات الطوارئ والتنبيهات' : 'Emergency & Notifications' }}</div>
            <div class="settings-card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label d-block">{{ $isAr ? 'تفعيل الطوارئ' : 'Emergency Enabled' }}</label>
                        <label><input type="checkbox" name="emergency_enabled" value="1" {{ old('emergency_enabled',($settings['emergency_enabled'] ?? '0')==='1') ? 'checked' : '' }}> emergency</label>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">{{ $isAr ? 'تفعيل التنبيهات' : 'Notifications Enabled' }}</label>
                        <label><input type="checkbox" name="notifications_enabled" value="1" {{ old('notifications_enabled',($settings['notifications_enabled'] ?? '0')==='1') ? 'checked' : '' }}> notifications</label>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'رقم الطوارئ' : 'Emergency Phone' }}</label>
                        <input class="form-control" name="emergency_phone" value="{{ old('emergency_phone',$settings['emergency_phone'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'نص الطوارئ' : 'Emergency Text' }}</label>
                        <input class="form-control" name="emergency_text" value="{{ old('emergency_text',$settings['emergency_text'] ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'أسعار أنواع الحجز' : 'Booking Type Prices' }}</div>
            <div class="settings-card-body">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'سعر الحجز العادي' : 'Regular Booking Price' }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="appointment_price_regular" value="{{ old('appointment_price_regular',$settings['appointment_price_regular'] ?? '300') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'سعر حجز VIP' : 'VIP Booking Price' }}</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="appointment_price_vip" value="{{ old('appointment_price_vip',$settings['appointment_price_vip'] ?? '600') }}">
                    </div>
                    <div class="col-12">
                        <span class="hint">{{ $isAr ? 'هذه الأسعار تُستخدم تلقائيًا في نموذج الحجز ويمكن تعديلها قبل الحفظ من الداشبورد.' : 'These prices are auto-used in booking forms and can be adjusted before saving in dashboard.' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="settings-card">
            <div class="settings-card-head">{{ $isAr ? 'إعدادات الفاتورة الإلكترونية' : 'E-Invoice Settings' }}</div>
            <div class="settings-card-body">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'الرقم الضريبي للبائع' : 'Seller VAT Number' }}</label>
                        <input class="form-control" name="seller_vat_number" value="{{ old('seller_vat_number',$settings['seller_vat_number'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'السجل التجاري' : 'Commercial Registration' }}</label>
                        <input class="form-control" name="seller_cr_number" value="{{ old('seller_cr_number',$settings['seller_cr_number'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'عنوان الفاتورة AR' : 'Invoice Address AR' }}</label>
                        <input class="form-control" name="seller_address_ar" value="{{ old('seller_address_ar',$settings['seller_address_ar'] ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">{{ $isAr ? 'عنوان الفاتورة EN' : 'Invoice Address EN' }}</label>
                        <input class="form-control" name="seller_address_en" value="{{ old('seller_address_en',$settings['seller_address_en'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'ملاحظة أسفل الفاتورة AR' : 'Invoice Footer Note AR' }}</label>
                        <textarea class="form-control" rows="2" name="invoice_footer_note_ar">{{ old('invoice_footer_note_ar',$settings['invoice_footer_note_ar'] ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ $isAr ? 'ملاحظة أسفل الفاتورة EN' : 'Invoice Footer Note EN' }}</label>
                        <textarea class="form-control" rows="2" name="invoice_footer_note_en">{{ old('invoice_footer_note_en',$settings['invoice_footer_note_en'] ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <span class="hint">{{ $isAr ? 'هذه البيانات تساعد في تجهيز الفاتورة الضريبية وQR. الاعتماد الرسمي من ZATCA يحتاج أيضًا XML وربط Fatoora.' : 'These values prepare tax invoice data and QR. Official ZATCA approval also requires XML generation and Fatoora integration.' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-end">
        <button class="btn btn-success px-4">Save</button>
    </div>
</form>
@endsection
