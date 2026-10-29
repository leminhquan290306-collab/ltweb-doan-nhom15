<?php

namespace App\Data;

use App\Models\SanPham;

class KhoSanPham
{
    /**
     * Đọc toàn bộ sản phẩm từ cơ sở dữ liệu MySQL thông qua PDO.
     */
    public function layTatCa(): array
    {
        // Gọi hàm kết nối hamCSDL() từ file db.php của nhóm
        $pdo = hamCSDL();

        // Truy vấn lấy sản phẩm kết hợp bảng danh_muc để lấy tên danh mục chuẩn
        $stmt = $pdo->query("
            SELECT sp.*, dm.ten AS ten_danh_muc 
            FROM san_pham sp 
            JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
            ORDER BY sp.id DESC
        ");
        $duLieu = $stmt->fetchAll();

        // Chuyển đổi dữ liệu từ mảng CSDL sang đối tượng SanPham y hệt cấu trúc cũ
        return array_map(
            function (array $sp) {
                return new SanPham(
                    (int) $sp['id'],
                    (string) $sp['ten'],
                    (string) ($sp['ten_danh_muc'] ?? 'Khác'), // Tên danh mục lấy từ bảng danh_muc
                    (float) $sp['gia'],
                    (int) ($sp['ton_kho'] ?? $sp['so_luong'] ?? 0), // Mapping số lượng / tồn kho
                    (string) ($sp['mo_ta'] ?? ''),
                    (string) ($sp['anh'] ?? 'images/default.jpg') // Đường dẫn ảnh
                );
            },
            $duLieu
        );
    }

    /**
     * Tìm sản phẩm theo ID từ MySQL.
     */
    public function timTheoId(int $id): ?SanPham
    {
        $pdo = hamCSDL();
        
        $stmt = $pdo->prepare("
            SELECT sp.*, dm.ten AS ten_danh_muc 
            FROM san_pham sp 
            JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
            WHERE sp.id = ?
        ");
        $stmt->execute([$id]);
        $sp = $stmt->fetch();

        if (!$sp) {
            return null;
        }

        return new SanPham(
            (int) $sp['id'],
            (string) $sp['ten'],
            (string) ($sp['ten_danh_muc'] ?? 'Khác'),
            (float) $sp['gia'],
            (int) ($sp['ton_kho'] ?? $sp['so_luong'] ?? 0),
            (string) ($sp['mo_ta'] ?? ''),
            (string) ($sp['anh'] ?? 'images/default.jpg')
        );
    }
}