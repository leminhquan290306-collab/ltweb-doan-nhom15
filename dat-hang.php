<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/tai-khoan.php';

use App\Services\GioHang;

// Kiểm tra đăng nhập bắt buộc trước khi đặt hàng
if (!daDangNhap()) {
    $_SESSION['dang_nhap_tb'] = 'Vui lòng đăng nhập để tiến hành đặt hàng.';
    redirect('dang-nhap.php');
}

$tieuDeTrang = 'Đặt hàng - Quà Tặng Thông Minh';
$gioHang = new GioHang();
$danhSach = $gioHang->laySanPham();

if (empty($danhSach)) {
    redirect('gio-hang.php');
}

$loi = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? null;
    if (!kiemTraCsrfToken(is_string($csrfToken) ? $csrfToken : null)) {
        http_response_code(419);
        exit('419 - CSRF token không hợp lệ.');
    }

    $pdo = hamCSDL();
    
    // Lấy chính xác ID người dùng từ session đang đăng nhập
    $nguoiDungId = $_SESSION['nguoi_dung_id'] ?? $_SESSION['user_id'] ?? $_SESSION['tai_khoan']['id'] ?? 0; 
    $tongTien = $gioHang->tongTien();

    if ($nguoiDungId <= 0) {
        redirect('dang-nhap.php');
    }

    try {
        // BẮT ĐẦU TRANSACTION AN TOÀN
        $pdo->beginTransaction();

        // 1. Tạo bản ghi đơn hàng liên kết với người dùng
        $stmtDon = $pdo->prepare("INSERT INTO don_hang (nguoi_dung_id, tong_tien, trang_thai, ngay_tao) VALUES (?, ?, 'moi', NOW())");
        $stmtDon->execute([$nguoiDungId, $tongTien]);
        $donHangId = $pdo->lastInsertId();

        // 2. Chuẩn bị câu lệnh thêm chi tiết đơn và trừ tồn kho
        $stmtChiTiet = $pdo->prepare("INSERT INTO chi_tiet_don (don_hang_id, san_pham_id, so_luong, don_gia) VALUES (?, ?, ?, ?)");
        $stmtTruKho  = $pdo->prepare("UPDATE san_pham SET ton_kho = ton_kho - ? WHERE id = ? AND ton_kho >= ?");

        foreach ($danhSach as $item) {
            $sp = $item['sanPham'];
            $soLuongMua = $item['soLuong'];
            $donGia = $sp->getGia();

            // Trừ tồn kho
            $stmtTruKho->execute([$soLuongMua, $sp->getId(), $soLuongMua]);
            if ($stmtTruKho->rowCount() === 0) {
                throw new RuntimeException("Sản phẩm '{$sp->getTen()}' không đủ số lượng trong kho!");
            }

            // Lưu chi tiết đơn hàng
            $stmtChiTiet->execute([$donHangId, $sp->getId(), $soLuongMua, $donGia]);
        }

        // HOÀN TẤT TRANSACTION
        $pdo->commit();

        // Xóa sạch giỏ hàng sau khi đặt thành công
        $gioHang->xoaTatCa();

        // Chuyển hướng sang trang lịch sử đơn hàng
        redirect("don-hang.php?thanh-cong={$donHangId}");

    } catch (Throwable $e) {
        $pdo->rollBack(); // HỦY GIAO DỊCH NẾU CÓ LỖI XẢY RA
        $loi = $e->getMessage();
    }
}

require_once __DIR__ . '/inc/header.php';
?>


<main>
    <section class="card trang-dat-hang">
        <h2>Xác nhận đặt hàng</h2>

        <?php if ($loi !== null): ?>
            <p role="alert" class="trang-dat-hang__loi"><?= e($loi) ?></p>
        <?php endif; ?>

        <h3>Danh sách món quà đã chọn:</h3>
        <ul class="trang-dat-hang__danh-sach">
            <?php foreach ($danhSach as $item): ?>
                <li class="trang-dat-hang__san-pham">
                    <strong><?= e($item['sanPham']->getTen()) ?></strong> -
                    Số lượng: <?= $item['soLuong'] ?> -
                    Thành tiền: <strong><?= number_format($item['thanhTien'], 0, ',', '.') ?> đ</strong>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="trang-dat-hang__tong-tien">
            Tổng thành tiền:
            <strong class="trang-dat-hang__gia">
                <?= number_format($gioHang->tongTien(), 0, ',', '.') ?> đ
            </strong>
        </p>

        <form method="POST" action="dat-hang.php">
            <input type="hidden" name="csrf_token" value="<?= e(taoCsrfToken()) ?>">
            <button type="submit" class="btn btn--chinh trang-dat-hang__nut">
                Xác nhận thanh toán & Đặt hàng
            </button>
            <a href="gio-hang.php" class="trang-dat-hang__quay-lai">Quay lại giỏ hàng</a>
        </form>
    </section>
</main>


<?php require_once __DIR__ . '/inc/footer.php'; ?>