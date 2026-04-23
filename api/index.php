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

// Route all Vercel PHP requests through Laravel's normal public front controller.
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../public') ?: __DIR__;

require __DIR__ . '/../public/index.php';
