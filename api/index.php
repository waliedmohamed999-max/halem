<?php

declare(strict_types=1);

// Route all Vercel PHP requests through Laravel's normal public front controller.
$_SERVER['SCRIPT_FILENAME'] = __FILE__;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../public') ?: __DIR__;

require __DIR__ . '/../public/index.php';
