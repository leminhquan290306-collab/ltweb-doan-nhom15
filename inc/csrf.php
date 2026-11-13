<?php
declare(strict_types=1);

/**
 * Tạo CSRF token cho phiên làm việc.
 */
function taoCsrfToken(): string
{
    if (
        !isset($_SESSION['csrf_token'])
        || !is_string($_SESSION['csrf_token'])
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Lấy CSRF token (tương thích mã nguồn hiện tại).
 */
function csrfToken(): string
{
    return taoCsrfToken();
}

/**
 * Kiểm tra CSRF token.
 */
function kiemTraCsrfToken(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Kiểm tra CSRF token (tương thích config.php).
 */
function csrfHopLe(?string $token): bool
{
    return kiemTraCsrfToken($token);
}

/**
 * Tạo trường ẩn CSRF cho form POST.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . e(taoCsrfToken())
        . '">';
}
