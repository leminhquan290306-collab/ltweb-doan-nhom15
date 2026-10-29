<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';

$tieuDeTrang = 'Đơn hàng của tôi - Quà Tặng Thông Minh';
$pdo = hamCSDL();

$nguoiDungId = $_SESSION['user_id'] ?? 2;
$thanhCongId = $_GET['thanh-cong'] ?? null;

// Lấy danh sách đơn hàng của người dùng
$stmt = $pdo->prepare("
    SELECT * FROM don_hang 
    WHERE nguoi_dung_id = ? 
    ORDER BY id DESC
");
$stmt->execute([$nguoiDungId]);
$danhSachDonHang = $stmt->fetchAll();

require_once __DIR__ . '/inc/header.php';
?>

<main>
    <section class="card">
        <h2>Lịch sử đơn hàng của tôi</h2>

        <?php if ($thanhCongId): ?>
            <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                🎉 Đặt hàng thành công! Mã đơn hàng của bạn là: <strong>#<?= e($thanhCongId) ?></strong>
            </div>
        <?php endif; ?>

        <?php if (empty($danhSachDonHang)): ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php else: ?>
            <?php foreach ($danhSachDonHang as $donHang): ?>
                <div style="border: 1px solid #ccc; border-radius: 6px; padding: 15px; margin-bottom: 15px;">
                    <h3>Đơn hàng #<?= $donHang['id'] ?> - <small>Ngày đặt: <?= $donHang['ngay_tao'] ?></small></h3>
                    <p>Trạng thái: <strong><?= e(strtoupper($donHang['trang_thai'])) ?></strong></p>
                    <p>Tổng tiền: <strong style="color: #e74c3c;"><?= number_format($donHang['tong_tien'], 0, ',', '.') ?> đ</strong></p>

                    <h4>Chi tiết sản phẩm:</h4>
                    <?php
                    $stmtCT = $pdo->prepare("
                        SELECT ct.*, sp.ten 
                        FROM chi_tiet_don ct 
                        JOIN san_pham sp ON ct.san_pham_id = sp.id 
                        WHERE ct.don_hang_id = ?
                    ");
                    $stmtCT->execute([$donHang['id']]);
                    $chiTiet = $stmtCT->fetchAll();
                    ?>
                    <ul>
                        <?php foreach ($chiTiet as $ct): ?>
                            <li><?= e($ct['ten']) ?> - Số lượng: <?= $ct['so_luong'] ?> - Đơn giá: <?= number_format($ct['don_gia'], 0, ',', '.') ?> đ</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>