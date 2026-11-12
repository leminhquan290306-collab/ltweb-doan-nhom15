<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

// Chỉ cho phép POST
yeuCauPost();

// Kiểm tra quyền quản trị
if (!laQuanTri()) {
    http_response_code(403);
    exit('403 - Bạn không có quyền thực hiện thao tác này.');
}

// Kiểm tra CSRF
$csrfToken = $_POST['csrf_token'] ?? null;

if (!csrfHopLe(
    is_string($csrfToken) ? $csrfToken : null
)) {
    http_response_code(403);
    exit('403 - Yêu cầu không hợp lệ.');
}

// Lấy ID sản phẩm từ POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id !== false && $id !== null && $id > 0) {
    try {
        $pdo = hamCSDL();

        $stmt = $pdo->prepare(
            'DELETE FROM san_pham WHERE id = ?'
        );

        $stmt->execute([$id]);

        $_SESSION['quan_tri_tb'] =
            $stmt->rowCount() > 0
                ? 'Xóa sản phẩm thành công!'
                : 'Không tìm thấy sản phẩm cần xóa.';

    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            $_SESSION['quan_tri_tb'] =
                'Không thể xóa sản phẩm này vì đã có trong lịch sử đơn hàng của khách!';
        } else {
            error_log('Lỗi xóa sản phẩm: ' . $e->getMessage());

            $_SESSION['quan_tri_tb'] =
                'Không thể xóa sản phẩm. Vui lòng thử lại!';
        }
    }
} else {
    $_SESSION['quan_tri_tb'] = 'Mã sản phẩm không hợp lệ.';
}

redirect('quan-tri.php?tab=san-pham');