<?php
// inc/csrf.php - Quản lý token CSRF chuẩn xác

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfField(): string
// in ra trong MỌI form method="post"
{
    return '<input type="hidden" name="csrf" value="' . csrfToken() . '">';
}

function csrfHopLe(): bool
// mã gửi lên có khớp mã trong phiên?
{
    // Hỗ trợ cả name="csrf" và name="csrf_token" để tránh lỗi cache trình duyệt
    $gui = $_POST['csrf'] ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return is_string($gui) && hash_equals(csrfToken(), $gui);
}