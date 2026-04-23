<?php

namespace App\Support;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Service;
use App\Models\Setting;
use App\Models\WorkingHour;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChatAssistantService
{
    public function shouldHandoff(string $message, string $locale): bool
    {
        $text = mb_strtolower(trim($message));

        $keywords = $locale === 'ar'
            ? ['موظف', 'خدمة العملاء', 'بشر', 'ادمن', 'مسؤول', 'التحدث مع شخص', 'تحويل']
            : ['human', 'agent', 'customer service', 'representative', 'support team', 'handoff'];

        foreach ($keywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function handoffReply(string $locale): string
    {
        return $locale === 'ar'
            ? 'تم تحويل المحادثة إلى خدمة العملاء. سيقوم أحد أفراد الفريق بالرد عليك هنا في أقرب وقت.'
            : 'This conversation has been handed over to customer service. A team member will reply here shortly.';
    }

    public function generateReply(ChatConversation $conversation, string $locale): string
    {
        $apiKey = trim((string) config('services.openai.api_key'));
        $baseUrl = rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/');
        $model = trim((string) config('services.openai.model', 'gpt-4.1-mini'));

        if ($apiKey === '' || $model === '' || ! $this->isSupportedApiBaseUrl($baseUrl)) {
            return $this->fallbackReply($conversation, $locale);
        }

        $messages = $conversation->messages()
            ->latest('id')
            ->limit(8)
            ->get()
            ->reverse()
            ->map(function (ChatMessage $message): array {
                $role = match ($message->sender_type) {
                    'customer' => 'user',
                    'admin', 'ai' => 'assistant',
                    default => 'system',
                };

                return [
                    'role' => $role,
                    'content' => $message->message,
                ];
            })
            ->values()
            ->all();

        array_unshift($messages, [
            'role' => 'system',
            'content' => ($locale === 'ar'
                ? 'أنت مساعد استقبال لعيادة أسنان. أجب باختصار وبشكل مهني. لا تخترع معلومات غير موجودة. استخدم فقط البيانات التالية عن العيادة والخدمات والفروع والأسعار التقريبية وساعات العمل. إذا لم تجد معلومة دقيقة، اطلب بيانات إضافية أو اعرض التحويل لخدمة العملاء.'
                : 'You are a dental clinic front-desk assistant. Reply briefly and professionally. Do not invent facts. Use only the following clinic, services, branches, estimated prices, and working-hours data. If exact info is unavailable, ask for more details or offer transfer to customer service.')
                . "\n\n" . $this->knowledgeBase($locale),
        ]);

        try {
            $response = Http::acceptJson()
                ->timeout(25)
                ->withToken($apiKey)
                ->post($baseUrl . '/responses', [
                    'model' => $model,
                    'input' => $messages,
                ]);

            if (! $response->ok()) {
                return $this->fallbackReply($conversation, $locale);
            }

            $outputText = data_get($response->json(), 'output_text');
            if (is_string($outputText) && trim($outputText) !== '') {
                return trim($outputText);
            }
        } catch (Throwable) {
            return $this->fallbackReply($conversation, $locale);
        }

        return $this->fallbackReply($conversation, $locale);
    }

    private function fallbackReply(ChatConversation $conversation, string $locale): string
    {
        $lastMessage = (string) optional($conversation->messages()->latest('id')->first())->message;
        $text = mb_strtolower($lastMessage);

        if (str_contains($text, 'سعر') || str_contains($text, 'تكلفة') || str_contains($text, 'price') || str_contains($text, 'cost')) {
            $pricing = $this->servicePricingHint($locale);

            return $locale === 'ar'
                ? ($pricing ?: 'لأعطيك ردًا أدق، اكتب الخدمة المطلوبة والفرع المناسب لك وسأجهز لك المعلومات المتاحة، أو اطلب التحويل لخدمة العملاء.')
                : ($pricing ?: 'To answer accurately, send the required service and preferred branch, and I will prepare the available information, or ask to be transferred to customer service.');
        }

        if (str_contains($text, 'موعد') || str_contains($text, 'حجز') || str_contains($text, 'appointment') || str_contains($text, 'booking')) {
            $branches = $this->branchesHint($locale);

            return $locale === 'ar'
                ? ('يمكنني مساعدتك مبدئيًا في الحجز. اكتب الفرع والخدمة واليوم المناسب لك، وإذا أردت تأكيدًا بشريًا سأحولك مباشرة لخدمة العملاء.' . ($branches ? ' ' . $branches : ''))
                : ('I can help with booking preliminarily. Send the branch, service, and preferred day, and if you want human confirmation I can transfer you directly to customer service.' . ($branches ? ' ' . $branches : ''));
        }

        return $locale === 'ar'
            ? 'أهلًا بك. اكتب سؤالك أو الخدمة المطلوبة وسأحاول مساعدتك فورًا، ويمكنني تحويلك لخدمة العملاء في أي وقت.'
            : 'Welcome. Send your question or required service and I will try to help immediately. I can transfer you to customer service at any time.';
    }

    private function knowledgeBase(string $locale): string
    {
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get()
            ->map(function (Service $service) use ($locale): string {
                $averagePrice = Appointment::query()
                    ->where('service_id', $service->id)
                    ->where('price', '>', 0)
                    ->avg('price');

                $title = $locale === 'ar' ? $service->title_ar : $service->title_en;
                $description = $locale === 'ar' ? $service->description_ar : $service->description_en;
                $priceText = $averagePrice
                    ? number_format((float) $averagePrice, 2)
                    : ($locale === 'ar' ? 'غير محدد' : 'not available');

                return "- {$title}: {$description}. " . ($locale === 'ar'
                    ? "متوسط السعر التقريبي: {$priceText}"
                    : "Estimated average price: {$priceText}");
            })
            ->implode("\n");

        $branches = Branch::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Branch $branch) use ($locale): string {
                $hours = WorkingHour::query()
                    ->where('branch_id', $branch->id)
                    ->where('is_open', true)
                    ->orderBy('day_of_week')
                    ->limit(2)
                    ->get()
                    ->map(fn (WorkingHour $hour) => ($locale === 'ar' ? $hour->day_label_ar : $hour->day_label_en) . ' ' . substr((string) $hour->open_at, 0, 5) . '-' . substr((string) $hour->close_at, 0, 5))
                    ->implode(', ');

                return "- {$branch->name}: {$branch->address}. " . ($locale === 'ar'
                    ? "الهاتف: {$branch->phone}. ساعات مختصرة: {$hours}"
                    : "Phone: {$branch->phone}. Sample hours: {$hours}");
            })
            ->implode("\n");

        $clinic = ($locale === 'ar' ? 'بيانات العيادة' : 'Clinic details')
            . ': '
            . Setting::getValue('site_name', 'Dr Halim Dental')
            . ' | '
            . Setting::getValue('site_phone', '')
            . ' | '
            . Setting::getValue('site_email', '');

        return trim($clinic . "\n\n" . ($locale === 'ar' ? 'الخدمات:' : 'Services:') . "\n" . $services . "\n\n" . ($locale === 'ar' ? 'الفروع:' : 'Branches:') . "\n" . $branches);
    }

    private function servicePricingHint(string $locale): ?string
    {
        $service = Service::query()->where('is_active', true)->orderBy('sort_order')->first();
        if (! $service) {
            return null;
        }

        $averagePrice = Appointment::query()
            ->where('service_id', $service->id)
            ->where('price', '>', 0)
            ->avg('price');

        if (! $averagePrice) {
            return null;
        }

        $title = $locale === 'ar' ? $service->title_ar : $service->title_en;

        return $locale === 'ar'
            ? "لدينا بيانات سعرية تقريبية لبعض الخدمات. مثال: {$title} بمتوسط تقريبي " . number_format((float) $averagePrice, 2) . '. إذا ذكرت اسم الخدمة والفرع أعطيك أقرب معلومة متاحة.'
            : "We have approximate pricing for some services. Example: {$title} with an estimated average of " . number_format((float) $averagePrice, 2) . '. If you mention the service and branch, I can give the closest available information.';
    }

    private function branchesHint(string $locale): ?string
    {
        $separator = $locale === 'ar' ? '، ' : ', ';
        $branches = Branch::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck($locale === 'ar' ? 'name_ar' : 'name_en')
            ->filter()
            ->take(4)
            ->implode($separator);

        if ($branches === '') {
            return null;
        }

        return $locale === 'ar'
            ? "الفروع المتاحة حاليًا: {$branches}."
            : "Available branches currently: {$branches}.";
    }

    private function isSupportedApiBaseUrl(string $baseUrl): bool
    {
        if ($baseUrl === '') {
            return false;
        }

        $scheme = parse_url($baseUrl, PHP_URL_SCHEME);

        return in_array($scheme, ['http', 'https'], true);
    }
}
