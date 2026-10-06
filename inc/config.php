<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/ham.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set(
    'error_log',
    dirname(__DIR__) . '/storage/logs/php-error.log'
);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

set_exception_handler(function (Throwable $e): void {
    error_log((string) $e);

    http_response_code(500);

    $trang500 = dirname(__DIR__) . '/500.php';

    if (is_file($trang500)) {
        require $trang500;
    } else {
        echo '500 - Đã xảy ra lỗi máy chủ.';
    }

    exit;
});