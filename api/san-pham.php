<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

use App\Data\KhoSanPham;

try {
    $khoSanPham = new KhoSanPham(hamCSDL());
    
    // Xem chi tiết 1 sản phẩm qua API (VD: api/san-pham.php?id=1)
    if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode([
            'thanh_cong' => false,
            'loi' => 'ID sản phẩm không hợp lệ.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
        $sp = $khoSanPham->timTheoId($id);
        if (!$sp) {
            http_response_code(404);
            echo json_encode(['thanh_cong' => false, 'loi' => 'Không tìm thấy sản phẩm'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode([
            'thanh_cong' => true,
            'du_lieu' => [
                'id' => $sp->getId(),
                'ten' => $sp->getTen(),
                'danh_muc' => $sp->getDanhMuc(),
                'gia' => $sp->getGia(),
                'so_luong' => $sp->getSoLuong(),
                'mo_ta' => $sp->getMoTa(),
                'anh' => $sp->getHinhAnh()
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Tìm kiếm hoặc lấy danh sách (VD: api/san-pham.php?q=tu-khoa)
    $tuKhoa = $_GET['q'] ?? '';
$tuKhoa = is_string($tuKhoa) ? trim($tuKhoa) : '';
$tuKhoa = mb_substr($tuKhoa, 0, 100, 'UTF-8');

$danhSach = $khoSanPham->trang(['q' => $tuKhoa], 'moi', 20, 0);

    $ketQua = [];
    foreach ($danhSach as $sp) {
        $ketQua[] = [
            'id' => $sp->getId(),
            'ten' => $sp->getTen(),
            'danh_muc' => $sp->getDanhMuc(),
            'gia' => $sp->getGia(),
            'so_luong' => $sp->getSoLuong(),
            'anh' => $sp->getHinhAnh()
        ];
    }

    echo json_encode([
        'thanh_cong' => true,
        'so_luong' => count($ketQua),
        'du_lieu' => $ketQua
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log('[API san-pham] ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'thanh_cong' => false,
        'loi' => 'Đã xảy ra lỗi máy chủ. Vui lòng thử lại sau.'
    ], JSON_UNESCAPED_UNICODE);
}