<?php
declare(strict_types=1);

use App\Data\KhoNguoiDung;

/**
 * Đăng ký tài khoản người dùng.
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

        $stmtCheck = $pdo->prepare(
            'SELECT id FROM nguoi_dung WHERE email = ? LIMIT 1'
        );
        $stmtCheck->execute([$email]);

        if ($stmtCheck->fetch()) {
            return 'Email này đã được đăng ký.';
        }

        $khoNguoiDung = new KhoNguoiDung();
        $matKhauHash = password_hash($matKhau, PASSWORD_DEFAULT);

        $khoNguoiDung->taoMoi($matKhauHash, $hoTen, $email);

        return null;
    } catch (Throwable $e) {
        error_log('Lỗi đăng ký: ' . $e->getMessage());
        return 'Không thể đăng ký lúc này. Vui lòng thử lại sau.';
    }
}

/**
 * Đăng nhập tài khoản.
 */
/**
 * Đăng nhập tài khoản.
 *
 * true  = đăng nhập thành công.
 * false = email hoặc mật khẩu không đúng.
 * null  = lỗi hệ thống.
 */
function dangNhapKetQua(string $email, string $matKhau): ?bool
{
    $email = strtolower(trim($email));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $matKhau === '') {
        return false;
    }

    try {
        $pdo = hamCSDL();

        $stmt = $pdo->prepare(
            'SELECT id, ho_ten, email, mat_khau, vai_tro
             FROM nguoi_dung
             WHERE email = ?
             LIMIT 1'
        );

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!is_array($user)) {
            return false;
        }

        $dbMatKhau = $user['mat_khau'] ?? '';

        if (
            !is_string($dbMatKhau)
            || !password_verify($matKhau, $dbMatKhau)
        ) {
            return false;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('Session chưa được khởi tạo.');
        }

        session_regenerate_id(true);

        $_SESSION['last_activity'] = time();

        $userId = (int) $user['id'];
        $vaiTro = (string) ($user['vai_tro'] ?? 'khach_hang');

        $_SESSION['nguoi_dung_id'] = $userId;
        $_SESSION['user_id'] = $userId;
        $_SESSION['ho_ten'] = (string) $user['ho_ten'];
        $_SESSION['email'] = (string) $user['email'];
        $_SESSION['vai_tro'] = $vaiTro;

        $_SESSION['tai_khoan'] = [
            'id' => $userId,
            'email' => (string) $user['email'],
            'hoTen' => (string) $user['ho_ten'],
            'vai_tro' => $vaiTro,
        ];

        return true;
    } catch (Throwable $e) {
        error_log('Lỗi hệ thống khi xử lý đăng nhập.');
        return null;
    }
}

/**
 * Hàm tương thích với các nơi đang gọi dangNhap().
 */
function dangNhap(string $email, string $matKhau): bool
{
    return dangNhapKetQua($email, $matKhau) === true;
}

/**
 * Kiểm tra trạng thái đăng nhập.
 */
function daDangNhap(): bool
{
    return isset($_SESSION['tai_khoan'])
        && is_array($_SESSION['tai_khoan'])
        && isset($_SESSION['tai_khoan']['id']);
}

/**
 * Kiểm tra quyền quản trị.
 */
function laQuanTri(): bool
{
    if (!daDangNhap()) {
        return false;
    }

    $vaiTro = $_SESSION['tai_khoan']['vai_tro']
        ?? $_SESSION['vai_tro']
        ?? '';

    return $vaiTro === 'quan_tri';
}

/**
 * Đăng xuất và hủy session.
 */
function dangXuat(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}
