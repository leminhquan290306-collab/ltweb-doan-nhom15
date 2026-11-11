<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

// Chỉ cho phép đăng xuất bằng POST
yeuCauPost();

// Kiểm tra CSRF
$token = $_POST['csrf_token'] ?? null;

if (!csrfHopLe(is_string($token) ? $token : null)) {
    http_response_code(403);
    exit('403 - Yêu cầu không hợp lệ.');
}

// Thực hiện đăng xuất
dangXuat();

redirect('dang-nhap.php');
