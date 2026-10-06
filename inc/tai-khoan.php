<?php

declare(strict_types=1);

function dangNhap(string $email, string $matKhau): bool
{
    $taiKhoanMau = [
        'email' => 'admin@gmail.com',
        'matKhau' => password_hash('123456', PASSWORD_DEFAULT),
    ];

    if (
        $email === $taiKhoanMau['email']
        && password_verify($matKhau, $taiKhoanMau['matKhau'])
    ) {
        session_regenerate_id(true);

        $_SESSION['tai_khoan'] = [
            'email' => $email,
            'vai_tro' => 'admin',
        ];

        return true;
    }

    return false;
}

function daDangNhap(): bool
{
    return isset($_SESSION['tai_khoan']);
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