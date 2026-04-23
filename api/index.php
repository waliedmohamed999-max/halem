<?php

declare(strict_types=1);

foreach ([
    '/tmp/framework/views',
    '/tmp/framework/cache/data',
    '/tmp/framework/sessions',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/logs',
] as $directory) {
    if (! is_dir($directory)) {
        @mkdir($directory, 0777, true);
    }
}

try {
    $_SERVER['SCRIPT_FILENAME'] = __FILE__;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $_SERVER['PHP_SELF'] = '/index.php';
    $_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../public') ?: __DIR__;

    require __DIR__ . '/../public/index.php';
} catch (Throwable $exception) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $isArabic = str_starts_with($requestUri, '/ar') || ! str_starts_with($requestUri, '/en');
    $lang = $isArabic ? 'ar' : 'en';
    $dir = $isArabic ? 'rtl' : 'ltr';
    $statusCode = 200;
    $appUrl = rtrim((string) ($_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'https://halem-cn4u.vercel.app'), '/');
    $whatsAppUrl = (string) ($_ENV['EMERGENCY_WHATSAPP_URL'] ?? 'https://wa.me/201028234921');
    $phoneNumber = (string) ($_ENV['EMERGENCY_PHONE'] ?? '01028234921');
    $safeMessage = htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8');

    @file_put_contents(
        'php://stderr',
        sprintf(
            "[vercel-fallback] %s in %s:%d\n",
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ),
        FILE_APPEND
    );

    http_response_code($statusCode);
    header('Content-Type: text/html; charset=UTF-8');

    $title = $isArabic ? 'مركز د. حليم لطب الأسنان' : 'Dr Halim Dental Center';
    $headline = $isArabic ? 'الموقع قيد التجهيز الآن' : 'The website is being finalized';
    $copy = $isArabic
        ? 'تم تفعيل صفحة طوارئ مؤقتة حتى يكتمل تشغيل النظام بالكامل. يمكنك التواصل أو الحجز الآن مباشرة.'
        : 'A temporary emergency page is active while the full system finishes booting. You can still contact or book directly now.';
    $bookLabel = $isArabic ? 'حجز موعد' : 'Book Appointment';
    $contactLabel = $isArabic ? 'واتساب مباشر' : 'WhatsApp';
    $callLabel = $isArabic ? 'اتصال مباشر' : 'Call Now';
    $homeLabel = $isArabic ? 'الصفحة الرئيسية' : 'Home';
    $note = $isArabic
        ? 'إذا كنت ترى هذه الصفحة، فهذا يعني أن نسخة الطوارئ تعمل بدلًا من النظام الكامل مؤقتًا.'
        : 'If you are seeing this, the emergency page is serving traffic temporarily instead of the full system.';

    echo <<<HTML
<!doctype html>
<html lang="{$lang}" dir="{$dir}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title}</title>
    <style>
        :root {
            --bg: #edf8f4;
            --panel: rgba(255,255,255,.92);
            --ink: #17324d;
            --soft: #5c7389;
            --brand: #0f8b8d;
            --brand-deep: #114c5f;
            --line: #d4e8e1;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(15,139,141,.16), transparent 28%),
                radial-gradient(circle at bottom left, rgba(17,76,95,.12), transparent 26%),
                linear-gradient(180deg, #f7fcfa 0%, var(--bg) 100%);
            display: grid;
            place-items: center;
            padding: 24px;
        }
        .card {
            width: min(100%, 880px);
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 32px;
            box-shadow: 0 24px 60px rgba(17,76,95,.12);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #e8f7f3;
            color: #136a6c;
            font-weight: 700;
            font-size: 14px;
        }
        h1 {
            margin: 20px 0 10px;
            font-size: clamp(32px, 5vw, 54px);
            line-height: 1.08;
            color: var(--brand-deep);
        }
        p {
            margin: 0;
            color: var(--soft);
            line-height: 1.9;
            font-size: 17px;
        }
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 52px;
            padding: 0 20px;
            border-radius: 16px;
            text-decoration: none;
            font-weight: 800;
            border: 1px solid transparent;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--brand), #11a6a8);
            color: #fff;
            box-shadow: 0 18px 34px rgba(15,139,141,.22);
        }
        .btn-outline {
            background: #fff;
            color: var(--brand-deep);
            border-color: #cfe4de;
        }
        .meta {
            margin-top: 28px;
            padding-top: 18px;
            border-top: 1px dashed #d3e4df;
            font-size: 14px;
            color: #6b8092;
        }
        .debug {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 14px;
            background: #f8fbfd;
            border: 1px solid #d9e6ef;
            font-family: Consolas, monospace;
            font-size: 12px;
            color: #4a6176;
            overflow: auto;
        }
        @media (max-width: 640px) {
            .card { padding: 22px; border-radius: 22px; }
            .actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="card">
        <span class="eyebrow">{$title}</span>
        <h1>{$headline}</h1>
        <p>{$copy}</p>
        <div class="actions">
            <a class="btn btn-primary" href="{$appUrl}/ar/appointments/create">{$bookLabel}</a>
            <a class="btn btn-outline" href="{$whatsAppUrl}" target="_blank" rel="noopener">{$contactLabel}</a>
            <a class="btn btn-outline" href="tel:{$phoneNumber}">{$callLabel}</a>
            <a class="btn btn-outline" href="{$appUrl}/ar">{$homeLabel}</a>
        </div>
        <div class="meta">{$note}</div>
        <div class="debug">{$safeMessage}</div>
    </main>
</body>
</html>
HTML;
}
