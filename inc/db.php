<?php

/**
 * Kết nối MySQL cho website nhóm 15.
 * Sử dụng database ltweb_doan_nhom15 trên XAMPP.
 * Trả về một kết nối PDO dùng chung trong mỗi request.
 * MSSV: 3120224120.
 */

function hamCSDL(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = '127.0.0.1';
    $dbname = 'ltweb_doan_nhom15';
    $username = 'root';
    $password = '';

    // Mảng các port phổ biến trên XAMPP để tự động dò tìm
    $ports = ['3307', '3306'];
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