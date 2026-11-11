<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

header('Content-Type: application/json; charset=UTF-8');

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
        // Kiểm tra CSRF cho API
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!csrfHopLe(is_string($token) ? $token : null)) {
            http_response_code(403);

            echo json_encode([
                'thanh_cong' => false,
                'loi' => 'Yêu cầu không hợp lệ hoặc CSRF token không đúng.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        // Hỗ trợ cả FormData và JSON
        $duLieu = $_POST;

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $json = json_decode(
                file_get_contents('php://input'),
                true
            );

            if (!is_array($json)) {
                http_response_code(400);

                echo json_encode([
                    'thanh_cong' => false,
                    'loi' => 'Dữ liệu JSON không hợp lệ.'
                ], JSON_UNESCAPED_UNICODE);

                exit;
            }

            $duLieu = $json;
        }

        $id = filter_var(
            $duLieu['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $soLuong = filter_var(
            $duLieu['so_luong'] ?? 1,
            FILTER_VALIDATE_INT
        );

        if ($id === false || $id === null || $id <= 0) {
            http_response_code(400);

            echo json_encode([
                'thanh_cong' => false,
                'loi' => 'ID sản phẩm không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        if ($soLuong === false || $soLuong === null || $soLuong <= 0) {
            http_response_code(400);

            echo json_encode([
                'thanh_cong' => false,
                'loi' => 'Số lượng sản phẩm không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);

            exit;
        }

        $gioHang->them($id, $soLuong);

        // Tính lại tổng số lượng sản phẩm
        $danhSach = $gioHang->laySanPham();
        $tongSoLuong = 0;

        foreach ($danhSach as $item) {
            $tongSoLuong += $item['soLuong'];
        }

        echo json_encode([
            'thanh_cong' => true,
            'thanh_bao' => 'Đã thêm sản phẩm vào giỏ hàng thành công!',
            'tong_so_luong' => $tongSoLuong
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    http_response_code(405);
    header('Allow: GET, POST');

    echo json_encode([
        'thanh_cong' => false,
        'loi' => 'Phương thức không được hỗ trợ.'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    error_log((string) $e);

    http_response_code(500);

    echo json_encode([
        'thanh_cong' => false,
        'loi' => 'Đã xảy ra lỗi máy chủ.'
    ], JSON_UNESCAPED_UNICODE);
}
