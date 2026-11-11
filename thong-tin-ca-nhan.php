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


<main class="container trang-thong-tin-ca-nhan">
    <!-- KHỐI 1: THÔNG TIN CÁ NHÂN -->
    <section class="card trang-thong-tin-ca-nhan__card trang-thong-tin-ca-nhan__thong-tin">
        <h2 class="trang-thong-tin-ca-nhan__tieu-de">Thông tin cá nhân</h2>

        <?php if ($thongTinKhach): ?>
            <div class="trang-thong-tin-ca-nhan__noi-dung">
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

        <div class="trang-thong-tin-ca-nhan__dang-xuat">
            <form method="POST" action="dang-xuat.php" class="trang-thong-tin-ca-nhan__form-dang-xuat">
                <?= csrfField() ?>
                <button type="submit" class="btn trang-thong-tin-ca-nhan__nut-dang-xuat">
                    Đăng xuất
                </button>
            </form>
        </div>
    </section>

    <!-- KHỐI 2: LỊCH SỬ MUA HÀNG -->
    <section class="card trang-thong-tin-ca-nhan__card">
        <h2 class="trang-thong-tin-ca-nhan__tieu-de">Lịch sử mua hàng</h2>

        <?php if (empty($danhSachDonHang)): ?>
            <p class="trang-thong-tin-ca-nhan__chua-co-don">
                Bạn chưa có đơn hàng nào trong hệ thống.
            </p>
            <p>
                <a href="danh-sach.php" class="btn btn--chinh trang-thong-tin-ca-nhan__nut-kham-pha">
                    Khám phá các sản phẩm
                </a>
            </p>
        <?php else: ?>
            <div class="trang-thong-tin-ca-nhan__bang-wrapper">
                <table class="trang-thong-tin-ca-nhan__bang">
                    <thead>
                        <tr class="trang-thong-tin-ca-nhan__bang-dau">
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($danhSachDonHang as $don): ?>
                            <tr class="trang-thong-tin-ca-nhan__bang-dong">
                                <td class="trang-thong-tin-ca-nhan__ma-don">#<?= e((string) $don['id']) ?></td>
                                <td><?= e((string) ($don['ngay_tao'] ?? 'N/A')) ?></td>
                                <td class="trang-thong-tin-ca-nhan__tong-tien">
                                    <?= number_format((float) ($don['tong_tien'] ?? 0), 0, ',', '.') ?> đ
                                </td>
                                <td>
                                    <span class="trang-thong-tin-ca-nhan__trang-thai">
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