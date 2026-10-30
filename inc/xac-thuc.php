<?php
/**
 * Tệp: inc/xac-thuc.php
 * Mô tả: Xác thực và phân quyền cho hệ thống đồ án nhóm 15.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        // Bỏ qua lỗi truy vấn ngầm, dùng fallback bên dưới
    }

    return [
        'id' => $uid,
        'email' => $_SESSION['email'] ?? $_SESSION['tai_khoan']['email'] ?? 'admin@ued.vn',
        'ho_ten' => $_SESSION['ho_ten'] ?? $_SESSION['tai_khoan']['hoTen'] ?? 'Quản trị viên',
        'vai_tro' => $_SESSION['vai_tro'] ?? $_SESSION['tai_khoan']['vai_tro'] ?? 'quan_tri'
    ];
}

/**
 * Kiểm tra quyền quản trị, nếu chưa đăng nhập hoặc không phải admin -> chuyển hướng hoặc 403
 */
function yeuCauQuyen(string $vaiTruYeuCau): void {
    if (!daDangNhap()) {
        header('Location: dang-nhap.php');
        exit;
    }

    if ($vaiTruYeuCau === 'quan_tri' && !laQuanTri()) {
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