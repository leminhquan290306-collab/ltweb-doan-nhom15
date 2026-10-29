<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

use App\Services\GioHang;

$gioHang = new GioHang();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Xem giỏ hàng qua API
        $danhSach = $gioHang->laySanPham();
        $tongTien = $gioHang->tongTien();
        $tongSoLuong = 0;
        foreach ($danhSach as $item) {
            $tongSoLuong += $item['soLuong'];
        }

        echo json_encode([
            'thanh_cong' => true,
            'tong_so_luong' => $tongSoLuong,
            'tong_tien' => $tongTien,
            'chi_tiet' => array_map(fn($item) => [
                'id' => $item['sanPham']->getId(),
                'ten' => $item['sanPham']->getTen(),
                'gia' => $item['sanPham']->getGia(),
                'so_luong' => $item['soLuong'],
                'thanh_tien' => $item['thanhTien'],
                'anh' => $item['sanPham']->getHinhAnh()
            ], $danhSach)
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($method === 'POST') {
        // Thêm sản phẩm vào giỏ hàng qua AJAX (POST)
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $soLuong = filter_input(INPUT_POST, 'so_luong', FILTER_VALIDATE_INT) ?? 1;

        if (!$id || $id <= 0) {
            http_response_code(400);
            echo json_encode(['thanh_cong' => false, 'loi' => 'ID sản phẩm không hợp lệ'], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $gioHang->them($id, max(1, $soLuong));

        // Tính lại tổng số lượng món trên header
        $danhSach = $gioHang->laySanPham();
        $tongSoLuong = 0;
        foreach ($danhSach as $item) {
            $tongSoLuong += $item['soLuong'];
        }

        echo json_encode([
            'thanh_cong' => true,
            'thong_bao' => 'Đã thêm sản phẩm vào giỏ hàng thành công!',
            'tong_so_luong' => $tongSoLuong
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['thanh_cong' => false, 'loi' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}