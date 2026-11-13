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
 * Kiểm tra và lấy dữ liệu chuỗi an toàn.
 */
function chuoi(mixed $value): string
{
    if (!is_string($value)) {
        return '';
    }

    return trim($value);
}
