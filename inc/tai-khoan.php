<?php

declare(strict_types=1);

/**
 * Đường dẫn lưu tài khoản khách đã đăng ký.
 */
function duongDanTaiKhoan(): string
{
    return dirname(__DIR__) . '/storage/tai-khoan.jsonl';
}

/**
 * Đọc tài khoản khách theo email.
 */
function timTaiKhoanTheoEmail(string $email): ?array
{
    $duongDan = duongDanTaiKhoan();

    if (!is_file($duongDan)) {
        return null;
    }

    $cacDong = file(
        $duongDan,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    if ($cacDong === false) {
        throw new RuntimeException('Không thể đọc dữ liệu tài khoản.');
    }

    $email = strtolower(trim($email));

    foreach ($cacDong as $dong) {
        $taiKhoan = json_decode($dong, true);

        if (
            is_array($taiKhoan)
            && strtolower((string) ($taiKhoan['email'] ?? '')) === $email
        ) {
            return $taiKhoan;
        }
    }

    return null;
}

/**
 * Đăng ký tài khoản khách.
 * Trả về thông báo lỗi nếu đăng ký thất bại, null nếu thành công.
 */
function dangKyTaiKhoan(
    string $hoTen,
    string $email,
    string $matKhau
): ?string {
    $hoTen = trim($hoTen);
    $email = strtolower(trim($email));

    if ($hoTen === '' || strlen($hoTen) > 100) {
        return 'Họ tên không được để trống và tối đa 100 ký tự.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email không hợp lệ.';
    }

    if (strlen($matKhau) < 8) {
        return 'Mật khẩu phải có ít nhất 8 ký tự.';
    }

    if ($email === 'admin@gmail.com') {
        return 'Email này không thể dùng để đăng ký tài khoản khách.';
    }

    $duongDan = duongDanTaiKhoan();
    $thuMuc = dirname($duongDan);

    if (!is_dir($thuMuc) && !mkdir($thuMuc, 0755, true) && !is_dir($thuMuc)) {
        throw new RuntimeException('Không thể tạo thư mục lưu tài khoản.');
    }

    $tep = fopen($duongDan, 'c+');

    if ($tep === false) {
        throw new RuntimeException('Không thể mở dữ liệu tài khoản.');
    }

    try {
        if (!flock($tep, LOCK_EX)) {
            throw new RuntimeException('Không thể khóa dữ liệu tài khoản.');
        }

        rewind($tep);
        $noiDung = stream_get_contents($tep);

        if ($noiDung === false) {
            throw new RuntimeException('Không thể đọc dữ liệu tài khoản.');
        }

        foreach (preg_split('/\r\n|\r|\n/', $noiDung) as $dong) {
            if (trim($dong) === '') {
                continue;
            }

            $taiKhoanCu = json_decode($dong, true);

            if (
                is_array($taiKhoanCu)
                && strtolower((string) ($taiKhoanCu['email'] ?? '')) === $email
            ) {
                return 'Email này đã được đăng ký.';
            }
        }

        $taiKhoanMoi = [
            'hoTen' => $hoTen,
            'email' => $email,
            'matKhauHash' => password_hash($matKhau, PASSWORD_DEFAULT),
            'vai_tro' => 'user',
            'thoiGianTao' => date(DATE_ATOM),
        ];

        $dongMoi = json_encode(
            $taiKhoanMoi,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if ($dongMoi === false) {
            throw new RuntimeException('Không thể mã hóa dữ liệu tài khoản.');
        }

        fseek($tep, 0, SEEK_END);

        if (fwrite($tep, $dongMoi . PHP_EOL) === false) {
            throw new RuntimeException('Không thể lưu tài khoản mới.');
        }

        fflush($tep);

        return null;
    } finally {
        flock($tep, LOCK_UN);
        fclose($tep);
    }
}

/**
 * Đăng nhập quản trị viên hoặc tài khoản khách.
 */
function dangNhap(string $email, string $matKhau): bool
{
    $email = strtolower(trim($email));

    // Tài khoản quản trị viên hiện tại.
    if (
        $email === 'admin@gmail.com'
        && password_verify(
            $matKhau,
            password_hash('123456', PASSWORD_DEFAULT)
        )
    ) {
        session_regenerate_id(true);

        $_SESSION['tai_khoan'] = [
            'email' => $email,
            'hoTen' => 'Quản trị viên',
            'vai_tro' => 'admin',
        ];

        return true;
    }

    // Tài khoản khách đã đăng ký.
    $taiKhoan = timTaiKhoanTheoEmail($email);

    if (
        $taiKhoan !== null
        && isset($taiKhoan['matKhauHash'])
        && password_verify($matKhau, $taiKhoan['matKhauHash'])
        && ($taiKhoan['vai_tro'] ?? '') === 'user'
    ) {
        session_regenerate_id(true);

        $_SESSION['tai_khoan'] = [
            'email' => $taiKhoan['email'],
            'hoTen' => $taiKhoan['hoTen'] ?? '',
            'vai_tro' => 'user',
        ];

        return true;
    }

    return false;
}

function daDangNhap(): bool
{
    return isset($_SESSION['tai_khoan'])
        && is_array($_SESSION['tai_khoan']);
}

function laQuanTri(): bool
{
    return daDangNhap()
        && ($_SESSION['tai_khoan']['vai_tro'] ?? '') === 'admin';
}

function dangXuat(): void
{
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
