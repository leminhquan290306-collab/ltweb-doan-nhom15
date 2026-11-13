<?php
namespace App\Data;

use App\Models\SanPham;
use PDO;

class KhoSanPham
{
    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        // Tự động gọi hamCSDL() nếu không truyền vào đối tượng PDO nào
        $this->db = $db ?? hamCSDL();
    }

    /**
     * Tạo điều kiện WHERE và mảng tham số dùng chung cho cả đếm và phân trang.
     */
    private function dieuKien(array $loc): array
    {
        $dk = [];
        $ts = [];

        // Tìm kiếm theo từ khóa (hỗ trợ tiếng Việt, chống lỗi cú pháp SQL với addcslashes)
        if (!empty($loc['q'])) {
            $dk[] = 'sp.ten LIKE ?';
            $ts[] = '%' . addcslashes($loc['q'], '%_\\') . '%';
        }

        // Lọc theo danh mục (danh_muc_id là số nguyên)
        if (!empty($loc['dm'])) {
            $dk[] = 'sp.danh_muc_id = ?';
            $ts[] = $loc['dm'];
        }

        // Lọc theo giá tối đa
        if (!empty($loc['giaToiDa'])) {
            $dk[] = 'sp.gia <= ?';
            $ts[] = $loc['giaToiDa'];
        }

        $where = $dk ? 'WHERE ' . implode(' AND ', $dk) : '';
        return [$where, $ts];
    }

    /**
     * Đếm tổng số sản phẩm khớp bộ lọc (phục vụ phân trang).
     */
    public function dem(array $loc): int
    {
        [$where, $ts] = $this->dieuKien($loc);
        $st = $this->db->prepare("SELECT COUNT(*) FROM san_pham sp $where");
        $st->execute($ts);
        return (int) $st->fetchColumn();
    }

    /**
     * Lấy danh sách sản phẩm theo trang, có sắp xếp và phân trang bằng SQL.
     */
    public function trang(array $loc, string $sx, int $soDong, int $boQua): array
    {
        [$where, $ts] = $this->dieuKien($loc);

        // Danh sách sắp xếp cho phép (bảo mật tuyệt đối chống SQL Injection)
        $orderBy = match ($sx) {
            'gia-tang' => 'sp.gia ASC, sp.id DESC',
            'gia-giam' => 'sp.gia DESC, sp.id DESC',
            'ten-tang' => 'sp.ten ASC, sp.id DESC',
            'ten-giam' => 'sp.ten DESC, sp.id DESC',
            default    => 'sp.id DESC', // 'moi' hoặc giá trị lạ
        };

        $sql = "SELECT sp.*, dm.ten AS ten_danh_muc 
                FROM san_pham sp 
                JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
                $where 
                ORDER BY $orderBy 
                LIMIT ? OFFSET ?";

        $st = $this->db->prepare($sql);

foreach ($ts as $index => $value) {
    $st->bindValue($index + 1, $value);
}

$st->bindValue(count($ts) + 1, max(1, $soDong), PDO::PARAM_INT);
$st->bindValue(count($ts) + 2, max(0, $boQua), PDO::PARAM_INT);

$st->execute();
        $duLieu = $st->fetchAll();

        return array_map(
            fn(array $sp) => new SanPham(
                (int) $sp['id'],
                (string) $sp['ten'],
                (string) ($sp['ten_danh_muc'] ?? 'Khác'),
                (float) $sp['gia'],
                (int) ($sp['ton_kho'] ?? 0),
                (string) ($sp['mo_ta'] ?? ''),
                (string) ($sp['anh'] ?? 'images/default.jpg')
            ),
            $duLieu
        );
    }

    public function timTheoId(int $id): ?SanPham
    {
        $stmt = $this->db->prepare("
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
            (int) ($sp['ton_kho'] ?? 0),
            (string) ($sp['mo_ta'] ?? ''),
            (string) ($sp['anh'] ?? 'images/default.jpg')
        );
    }
    
    public function layCungDanhMuc(int $danhMucId, int $sanPhamIdHienTai, int $gioiHan = 4): array
    {
        $stmt = $this->db->prepare("
            SELECT sp.*, dm.ten AS ten_danh_muc 
            FROM san_pham sp 
            JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
            WHERE sp.danh_muc_id = ? AND sp.id != ?
            ORDER BY RAND() 
            LIMIT ?
        ");
        $stmt->bindValue(1, $danhMucId, PDO::PARAM_INT);
$stmt->bindValue(2, $sanPhamIdHienTai, PDO::PARAM_INT);
$stmt->bindValue(3, max(1, min($gioiHan, 20)), PDO::PARAM_INT);
$stmt->execute();
        $duLieu = $stmt->fetchAll();

        return array_map(
            fn(array $sp) => new SanPham(
                (int) $sp['id'],
                (string) $sp['ten'],
                (string) ($sp['ten_danh_muc'] ?? 'Khác'),
                (float) $sp['gia'],
                (int) ($sp['ton_kho'] ?? 0),
                (string) ($sp['mo_ta'] ?? ''),
                (string) ($sp['anh'] ?? 'images/default.jpg')
            ),
            $duLieu
        );
    }
}