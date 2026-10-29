<?php

declare(strict_types=1);

use App\Data\KhoNguoiDung;

/**
 * Đăng ký tài khoản khách vào CSDL MySQL thông qua KhoNguoiDung.
 */
function dangKyTaiKhoan(
    string $hoTen,
    string $email,
    string $matKhau
): ?string {
    $hoTen = trim($hoTen);
    $email = strtolower(trim($email));

    if ($hoTen === '' || mb_strlen($hoTen) > 100) {
        return 'Họ tên không được để trống và tối đa 100 ký tự.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email không hợp lệ.';
    }

    if (mb_strlen($matKhau) < 8) {
        return 'Mật khẩu phải có ít nhất 8 ký tự.';
    }

    try {
        $pdo = hamCSDL();
        
        // Kiểm tra xem email đã tồn tại trong CSDL chưa
        $stmtCheck = $pdo->prepare("SELECT id FROM nguoi_dung WHERE email = ? LIMIT 1");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            return 'Email này đã được đăng ký.';
        }

        $khoNguoiDung = new KhoNguoiDung();
        $matKhauHash = password_hash($matKhau, PASSWORD_DEFAULT);

        // Gọi hàm tạo mới trong KhoNguoiDung OOP
        $khoNguoiDung->taoMoi($matKhauHash, $hoTen, $email);

        return null; // Thành công
    } catch (Throwable $e) {
        error_log('Lỗi đăng ký: ' . $e->getMessage());
        return 'Không thể đăng ký lúc này. Vui lòng thử lại sau.';
    }
}

/**
 * Đăng nhập dựa trên cơ sở dữ liệu MySQL bảng nguoi_dung.
 */
function dangNhap(string $email, string $matKhau): bool
{
    $email = strtolower(trim($email));

    try {
        $pdo = hamCSDL();
        
        $stmt = $pdo->prepare("SELECT * FROM nguoi_dung WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($matKhau, $user['mat_khau'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            session_regenerate_id(true);

            $userId = (int) $user['id'];
            $vaiTro = $user['vai_tro'] ?? 'khach_hang';

            // Thiết lập đầy đủ các biến session để tương thích với cả Đặt hàng và Quản trị
            $_SESSION['nguoi_dung_id'] = $userId;
            $_SESSION['user_id'] = $userId;
            $_SESSION['ho_ten'] = $user['ho_ten'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['vai_tro'] = $vaiTro;

            // Hỗ trợ thêm mảng $_SESSION['tai_khoan'] nếu các trang cũ đang dùng
            $_SESSION['tai_khoan'] = [
                'id' => $userId,
                'email' => $user['email'],
                'hoTen' => $user['ho_ten'],
                'vai_tro' => $vaiTro,
            ];

            return true;
        }
    } catch (Throwable $e) {
        error_log('Lỗi đăng nhập: ' . $e->getMessage());
    }

    return false;
}

/**
 * Kiểm tra xem người dùng đã đăng nhập chưa.
 */
function daDangNhap(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['nguoi_dung_id']) 
        || isset($_SESSION['user_id']) 
        || (isset($_SESSION['tai_khoan']) && is_array($_SESSION['tai_khoan']));
}

/**
 * Kiểm tra xem có phải quản trị viên không.
 */
function laQuanTri(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // Kiểm tra linh hoạt các biến session vai trò
    $vaiTro = $_SESSION['vai_tro'] ?? $_SESSION['tai_khoan']['vai_tro'] ?? '';
    
    // Nếu email đăng nhập là admin@ued.vn hoặc vai trò là quan_tri/admin thì cho qua luôn
    $emailDangNhap = $_SESSION['email'] ?? $_SESSION['tai_khoan']['email'] ?? '';
    
    if ($emailDangNhap === 'admin@ued.vn' || $vaiTro === 'admin' || $vaiTro === 'quan_tri') {
        return true;
    }

    return false;
}

/**
 * Đăng xuất tài khoản.
 */
function dangXuat(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}