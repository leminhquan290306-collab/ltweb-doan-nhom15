
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
    $port = '3307';
    $dbname = 'ltweb_doan_nhom15';
    $username = 'root';
    $password = '';

    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
