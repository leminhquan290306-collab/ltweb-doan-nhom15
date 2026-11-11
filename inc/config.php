<?php
/**
 * Tệp: inc/config.php
 * Mô tả: Cấu hình hệ thống, phiên làm việc, xử lý ngoại lệ và bảo mật CSRF tập trung.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/ham.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/xac-thuc.php';

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

// Kiểm tra CSRF tập trung an toàn tuyệt đối, tương thích với hàm trong ham.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenGuiLen = $_POST['csrf'] ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $tokenGuiLen = is_string($tokenGuiLen) ? $tokenGuiLen : null;

    if ($tokenGuiLen === null || !csrfHopLe($tokenGuiLen)) {
        http_response_code(403);
        echo '<div style="max-width: 500px; margin: 80px auto; padding: 30px; background: #fff5f5; border: 1px solid #feb2b2; border-radius: 8px; font-family: sans-serif; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">';
        echo '<h2 style="color: #e53e3e; margin-top: 0; font-size: 22px;">⚠️ Cảnh báo bảo mật (Lỗi CSRF)</h2>';
        echo '<p style="color: #4a5568; font-size: 15px; line-height: 1.5;">Yêu cầu của bạn không hợp lệ hoặc phiên làm việc đã hết hạn. Vui lòng tải lại trang.</p>';
        echo '<a href="javascript:history.back()" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #3182ce; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 14px;">Quay lại trang trước</a>';
        echo '</div>';
        exit;
    }
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