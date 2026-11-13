<?php
/**
 * Tệp: inc/config.php
 * Cấu hình hệ thống, phiên làm việc và bảo mật.
 */

declare(strict_types=1);

// Cấu hình lỗi và ghi log.
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');

ini_set(
    'error_log',
    dirname(__DIR__) . '/storage/logs/php-error.log'
);

// Nạp các thành phần hệ thống.
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Nạp biến môi trường từ file .env.
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

require_once __DIR__ . '/ham.php';
require_once __DIR__ . '/bao-mat.php';
require_once __DIR__ . '/csrf.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/xac-thuc.php';

// Cấu hình múi giờ.
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Xác định kết nối HTTPS.
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? '') == 443)
);

// Cấu hình bảo mật session.
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.use_trans_sid', '0');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', $isHttps ? '1' : '0');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', '1800');

// Khởi tạo session.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();

    // Tự động hết hạn phiên sau 30 phút không hoạt động.
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
// HTTP Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Content Security Policy (CSP)
    header(
        "Content-Security-Policy: "
        . "default-src 'self'; "
        . "base-uri 'self'; "
        . "object-src 'none'; "
        . "frame-ancestors 'self'; "
        . "form-action 'self'; "
        . "script-src 'self' https://cdn.jsdelivr.net; "
        . "script-src-attr 'none'; "
        . "style-src 'self' https://cdn.jsdelivr.net; "
        . "style-src-attr 'none'; "
        . "img-src 'self' data:; "
        . "font-src 'self' data:; "
        . "connect-src 'self' https://api.open-meteo.com;"
    );
}

// Kiểm tra CSRF tập trung cho mọi yêu cầu POST.

// Central POST CSRF guard:
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $tokenGuiLen = $_POST['csrf']
        ?? $_POST['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? null;

    if (
        !is_string($tokenGuiLen)
        || !csrfHopLe($tokenGuiLen)
    ) {
        ghiNhatKyBaoMat('csrf_sai', [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);

        http_response_code(403);

        $duongDan = parse_url(
            $_SERVER['REQUEST_URI'] ?? '',
            PHP_URL_PATH
        ) ?: '';

        $laApiJson = (
            str_contains($duongDan, '/api/')
            || stripos(
                $_SERVER['CONTENT_TYPE'] ?? '',
                'application/json'
            ) !== false
        );

        if ($laApiJson) {
            header('Content-Type: application/json; charset=UTF-8');

            echo json_encode([
                'thanh_cong' => false,
                'loi' => 'Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.',
            ], JSON_UNESCAPED_UNICODE);
        } else {
            exit('403 - Yêu cầu không hợp lệ hoặc phiên làm việc đã hết hạn.');
        }

        exit;
    }
}


// Xử lý ngoại lệ và ghi log.
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
