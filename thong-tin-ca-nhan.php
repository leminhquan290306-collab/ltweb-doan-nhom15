<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
require_once __DIR__ . '/inc/tai-khoan.php';

// Kiểm tra nếu chưa đăng nhập thì chuyển hướng về trang đăng nhập
if (!daDangNhap()) {
    redirect('dang-nhap.php');
}

$tieuDeTrang = 'Thông tin cá nhân - Quà Tặng Thông Minh';

// Lấy ID người dùng từ Session
$userId = $_SESSION['nguoi_dung_id'] ?? $_SESSION['user_id'] ?? $_SESSION['tai_khoan']['id'] ?? 0;

$thongTinKhach = null;
$danhSachDonHang = [];

try {
    $pdo = hamCSDL();

    // 1. Truy vấn lấy thông tin mới nhất của khách hàng từ bảng nguoi_dung
    $stmtUser = $pdo->prepare("SELECT id, ho_ten, email, vai_tro, ngay_tao FROM nguoi_dung WHERE id = ? LIMIT 1");
    $stmtUser->execute([$userId]);
    $thongTinKhach = $stmtUser->fetch(PDO::FETCH_ASSOC);

    // 2. Truy vấn lấy lịch sử đơn hàng của khách từ bảng don_hang
    $stmtOrders = $pdo->prepare("SELECT * FROM don_hang WHERE nguoi_dung_id = ? ORDER BY id DESC");
    $stmtOrders->execute([$userId]);
    $danhSachDonHang = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    error_log('Lỗi tải thông tin tài khoản: ' . $e->getMessage());
}

require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 900px; margin: 30px auto; padding: 0 15px;">
    <!-- KHỐI 1: THÔNG TIN CÁ NHÂN -->
    <section class="card" style="padding: 20px; margin-bottom: 30px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0;">Thông tin cá nhân</h2>
        
        <?php if ($thongTinKhach): ?>
            <div style="line-height: 1.8; font-size: 16px;">
                <p><strong>Họ và tên:</strong> <?= e((string) $thongTinKhach['ho_ten']) ?></p>
                <p><strong>Email:</strong> <?= e((string) $thongTinKhach['email']) ?></p>
                <p><strong>Vai trò:</strong> <?= e($thongTinKhach['vai_tro'] === 'quan_tri' || $thongTinKhach['vai_tro'] === 'admin' ? 'Quản trị viên' : 'Khách hàng') ?></p>
                <?php if (!empty($thongTinKhach['ngay_tao'])): ?>
                    <p><strong>Ngày tham gia:</strong> <?= e((string) $thongTinKhach['ngay_tao']) ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p><strong>Họ và tên:</strong> <?= e($_SESSION['ho_ten'] ?? $_SESSION['tai_khoan']['hoTen'] ?? 'Khách hàng') ?></p>
            <p><strong>Email:</strong> <?= e($_SESSION['email'] ?? $_SESSION['tai_khoan']['email'] ?? 'Chưa cập nhật') ?></p>
        <?php endif; ?>

        <div style="margin-top: 15px;">
            <a href="dang-xuat.php" class="btn" style="background-color: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Đăng xuất</a>
        </div>
    </section>

    <!-- KHỐI 2: LỊCH SỬ MUA HÀNG -->
    <section class="card" style="padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0;">Lịch sử mua hàng</h2>

        <?php if (empty($danhSachDonHang)): ?>
            <p style="padding: 15px 0;">Bạn chưa có đơn hàng nào trong hệ thống.</p>
            <p><a href="danh-sach.php" class="btn btn--chinh" style="padding: 8px 16px; text-decoration: none;">Khám phá các sản phẩm</a></p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; text-align: left;">
                    <thead>
                        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px;">Mã đơn</th>
                            <th style="padding: 12px;">Ngày đặt</th>
                            <th style="padding: 12px;">Tổng tiền</th>
                            <th style="padding: 12px;">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachDonHang as $don): ?>
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; font-weight: bold;">#<?= e((string) $don['id']) ?></td>
                                <td style="padding: 12px;"><?= e((string) ($don['ngay_tao'] ?? 'N/A')) ?></td>
                                <td style="padding: 12px; color: #d9534f; font-weight: bold;">
                                    <?= number_format((float) ($don['tong_tien'] ?? 0), 0, ',', '.') ?> đ
                                </td>
                                <td style="padding: 12px;">
                                    <span style="background: #e2e3e5; padding: 4px 8px; border-radius: 4px; font-size: 14px;">
                                        <?= e((string) ($don['trang_thai'] ?? 'Đang xử lý')) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>