<?php

declare(strict_types=1);

/**
 * Escape dữ liệu trước khi đưa ra HTML.
 */
function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

/**
 * Chuyển hướng và dừng xử lý.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Chỉ cho phép request GET.
 */
function yeuCauGet(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');
        exit('405 - Phương thức không được phép.');
    }
}

/**
 * Chỉ cho phép request POST.
 */
function yeuCauPost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('405 - Phương thức không được phép.');
    }
}

/**
 * Kiểm tra CSRF token.
 */
function taoCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function kiemTraCsrfToken(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Lấy mã CSRF
function csrfToken(): string
{
    return taoCsrfToken();
}

// In trường CSRF cho form POST (Đã sửa name="csrf" chuẩn xác)
function csrfField(): string
{
    return '<input type="hidden" name="csrf" value="' .
        htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') .
        '">';
}

// Kiểm tra mã CSRF
function csrfHopLe(?string $token): bool
{
    return kiemTraCsrfToken($token);
}