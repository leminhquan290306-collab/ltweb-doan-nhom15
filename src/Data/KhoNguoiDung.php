<?php

namespace App\Data;

use App\Models\NguoiDung;
use PDO;

class KhoNguoiDung
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = hamCSDL();
    }

    /**
     * Thêm tài khoản người dùng mới khớp chuẩn các cột trong bảng nguoi_dung
     */
    public function taoMoi(
        string $matKhauMaHoa,
        string $hoTen,
        string $email
    ): int {
        $sql = "INSERT INTO nguoi_dung (email, mat_khau, ho_ten, vai_tro) 
                VALUES (:email, :mat_khau, :ho_ten, 'khach_hang')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email'    => $email,
            ':mat_khau' => $matKhauMaHoa,
            ':ho_ten'   => $hoTen,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function timTheoId(int $id): ?NguoiDung
    {
        $sql = "SELECT * FROM nguoi_dung WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new NguoiDung(
            (int) $row['id'],
            $row['mat_khau'],
            $row['ho_ten'],
            $row['email'],
            $row['vai_tro'],
            $row['ngay_tao']
        );
    }
}