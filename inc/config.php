<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/ham.php';
require_once __DIR__ . '/db.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

error_reporting(E_ALL);
ini_set('display_errors', '1');

ini_set(
    'error_log',
    dirname(__DIR__) . '/storage/logs/php-error.log'
);

// Cấu hình bảo mật session
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') == 443)
);

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', $isHttps ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', '1800');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();

    // Tự động hết hạn sau 30 phút không hoạt động
    $thoiGianHienTai = time();
    $thoiGianHoatDongCuoi = $_SESSION['last_activity'] ?? 0;

    if (
        $thoiGianHoatDongCuoi > 0
        && ($thoiGianHienTai - $thoiGianHoatDongCuoi) > 1800
    ) {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    $_SESSION['last_activity'] = $thoiGianHienTai;
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