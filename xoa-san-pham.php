<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

if (!laQuanTri()) {
    redirect('dang-nhap.php');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $pdo = hamCSDL();
        $stmt = $pdo->prepare("DELETE FROM san_pham WHERE id = ?");
        $stmt->execute([$id]);

        $_SESSION['quan_tri_tb'] = 'Xóa sản phẩm thành công!';
    } catch (PDOException $e) {
        // Bắt lỗi khóa ngoại (Mã lỗi 1451 khi sản phẩm đã nằm trong đơn hàng)
        if ($e->getCode() == 23000) {
            $_SESSION['quan_tri_tb'] = 'Không thể xóa sản phẩm này vì đã có trong lịch sử đơn hàng của khách!';
        } else {
            $_SESSION['quan_tri_tb'] = 'Lỗi khi xóa sản phẩm: ' . $e->getMessage();
        }
    }
}

redirect('quan-tri.php?tab=san-pham');