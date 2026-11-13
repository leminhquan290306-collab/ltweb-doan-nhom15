<?php
/**
 * Tệp: inc/xac-thuc.php
 * Mô tả: Xác thực và phân quyền cho hệ thống đồ án nhóm 15.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';
// Bắt buộc nạp tệp tài khoản để có sẵn các hàm daDangNhap() và laQuanTri()
if (!function_exists('daDangNhap')) {
    require_once __DIR__ . '/tai-khoan.php';
}

/**
 * Lấy thông tin người dùng đang đăng nhập
 */
function nguoiDung(): ?array {
    if (!daDangNhap()) {
        return null;
    }

    $uid = $_SESSION['nguoi_dung_id'] ?? $_SESSION['user_id'] ?? $_SESSION['tai_khoan']['id'] ?? null;
    if (!$uid) {
        return null;
    }

    try {
        $pdo = function_exists('db') ? db() : hamCSDL();
        $st = $pdo->prepare('SELECT id, email, ho_ten, vai_tro FROM nguoi_dung WHERE id = ?');
        $st->execute([$uid]);
        $res = $st->fetch(PDO::FETCH_ASSOC);
        if ($res !== false) {
            return $res;
        }
        } catch (Throwable $e) {
        error_log('Lỗi lấy thông tin người dùng: ' . $e->getMessage());
        return null;
    }

    return null;
}

/**
 * Kiểm tra quyền quản trị, nếu chưa đăng nhập hoặc không phải admin -> chuyển hướng hoặc 403.
 */
function yeuCauQuyen(string $vaiTruYeuCau): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if (!daDangNhap()) {
        ghiNhatKyBaoMat('cam_truy_cap', [
            'ip' => $ip,
        ]);

        header('Location: dang-nhap.php');
        exit;
    }

    if ($vaiTruYeuCau === 'quan_tri' && !laQuanTri()) {
        ghiNhatKyBaoMat('cam_truy_cap', [
            'ip' => $ip,
        ]);

        http_response_code(403);

        $trang403 = __DIR__ . '/../403.php';

        if (file_exists($trang403)) {
            require $trang403;
        } else {
            echo '<h1>403 Forbidden</h1><p>Bạn không có quyền truy cập trang quản trị này.</p>';
        }

        exit;
    }
}