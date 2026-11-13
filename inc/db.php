<?php

/**
 * Tệp: inc/db.php
 * Mô tả: Kết nối MySQL cho website nhóm 15.
 * Sử dụng database ltweb_doan_nhom15 trên XAMPP.
 * MSSV: 3120224120.
 */

declare(strict_types=1);

function hamCSDL(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbname = $_ENV['DB_NAME'] ?? '';
$username = $_ENV['DB_USER'] ?? '';
$password = $_ENV['DB_PASS'] ?? '';

// Đọc danh sách port từ .env.
$ports = array_filter(
    array_map('trim', explode(',', $_ENV['DB_PORTS'] ?? '3307,3306')),
    static fn($port) => ctype_digit($port)
);
    $pdo = null;
    $lastError = null;

    foreach ($ports as $port) {
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            break; // Kết nối thành công thì thoát vòng lặp
        } catch (PDOException $e) {
            $lastError = $e;
        }
    }

    // Nếu không kết nối được với cả 2 port thì bắn ngoại lệ
    if (!$pdo) {
        throw new PDOException("Không thể kết nối CSDL ở cả cổng 3306 và 3307. Lỗi: " . ($lastError ? $lastError->getMessage() : 'Không rõ'));
    }

    return $pdo;
}

/**
 * Hàm alias db() để tương thích với các đoạn code gọi kết nối kiểu ngắn gọn.
 */
if (!function_exists('db')) {
    function db(): PDO
    {
        return hamCSDL();
    }
}