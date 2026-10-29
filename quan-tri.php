<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

use App\Data\KhoSanPham;

if (!laQuanTri()) {
    redirect('dang-nhap.php');
}

$khoSanPham = new KhoSanPham(hamCSDL());

// Xử lý phân trang cho danh sách quản trị sản phẩm
$moiTrang = 10;
$tongSoDong = $khoSanPham->dem([]);
$tongSoTrang = max(1, (int) ceil($tongSoDong / $moiTrang));
$hienTai = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
$hienTai = min($hienTai, $tongSoTrang);
$boQua = ($hienTai - 1) * $moiTrang;

$danhSachSanPham = $khoSanPham->trang([], 'moi', $moiTrang, $boQua);

// Lấy danh sách liên hệ như code cũ của ông
$danhSachLienHe = [];
try {
    $pdo = hamCSDL();
    $stmt = $pdo->query("SELECT * FROM lien_he ORDER BY id DESC");
    $danhSachLienHe = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $danhSachLienHe = [];
}

$tab = $_GET['tab'] ?? 'san-pham';
$tieuDeTrang = 'Trang quản trị hệ thống';
$baseUrl = '';

$thongBao = $_SESSION['quan_tri_tb'] ?? '';
unset($_SESSION['quan_tri_tb']);

require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 1100px; margin: 30px auto; padding: 0 15px;">

    <!-- HEADER DASHBOARD -->
    <section style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2>Khu vực Quản trị (Admin)</h2>
            <p>Xin chào: <strong><?= e($_SESSION['email'] ?? $_SESSION['tai_khoan']['email'] ?? 'Admin') ?></strong></p>
        </div>
        <div>
            <a href="quan-tri.php?tab=san-pham" class="btn" style="padding: 8px 15px; background: <?= $tab === 'san-pham' ? '#5a20cb' : '#ccc' ?>; color: #fff; text-decoration: none; border-radius: 4px; margin-right: 5px;">Quản lý Sản phẩm</a>
            <a href="quan-tri.php?tab=lien-he" class="btn" style="padding: 8px 15px; background: <?= $tab === 'lien-he' ? '#5a20cb' : '#ccc' ?>; color: #fff; text-decoration: none; border-radius: 4px; margin-right: 15px;">Quản lý Liên hệ (<?= count($danhSachLienHe) ?>)</a>
            <a href="dang-xuat.php" style="color: #dc3545; text-decoration: none; font-weight: bold;">Đăng xuất</a>
        </div>
    </section>

    <?php if ($thongBao !== ''): ?>
        <p style="padding: 12px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px;"><?= e($thongBao) ?></p>
    <?php endif; ?>

    <!-- TAB 1: QUẢN LÝ SẢN PHẨM -->
    <?php if ($tab === 'san-pham'): ?>
        <section style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="margin: 0;">Danh sách sản phẩm</h3>
                <a href="them-san-pham.php" style="background: #27ae60; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;">+ Thêm sản phẩm mới</a>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 10px;">ID</th>
                            <th style="padding: 10px;">Ảnh</th>
                            <th style="padding: 10px;">Tên sản phẩm</th>
                            <th style="padding: 10px;">Danh mục</th>
                            <th style="padding: 10px;">Giá</th>
                            <th style="padding: 10px;">Tồn kho</th>
                            <th style="padding: 10px; text-align: center;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachSanPham)): ?>
                            <tr><td colspan="7" style="padding: 15px; text-align: center;">Chưa có sản phẩm nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($danhSachSanPham as $sp): ?>
                                <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 10px;"><?= e((string)$sp->getId()) ?></td>
                                    <td style="padding: 10px;"><img src="<?= e($sp->getHinhAnh()) ?>" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;"></td>
                                    <td style="padding: 10px; font-weight: 500;"><?= e($sp->getTen()) ?></td>
                                    <td style="padding: 10px;"><?= e($sp->getDanhMuc()) ?></td>
                                    <td style="padding: 10px; color: #e74c3c; font-weight: bold;"><?= number_format($sp->getGia(), 0, ',', '.') ?> đ</td>
                                    <td style="padding: 10px;"><?= e((string)$sp->getSoLuong()) ?></td>
                                    <td style="padding: 10px; text-align: center;">
                                        <a href="sua-san-pham.php?id=<?= e((string)$sp->getId()) ?>" style="background: #f39c12; color: #fff; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 13px; margin-right: 5px;">Sửa</a>
                                        <a href="xoa-san-pham.php?id=<?= e((string)$sp->getId()) ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');" style="background: #e74c3c; color: #fff; padding: 5px 10px; border-radius: 3px; text-decoration: none; font-size: 13px;">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang quản trị -->
            <?php if ($tongSoTrang > 1): ?>
                <nav style="margin-top: 20px; display: flex; justify-content: center; gap: 5px;">
                    <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
                        <a href="quan-tri.php?tab=san-pham&trang=<?= $i ?>" style="padding: 6px 12px; border: 1px solid #ccc; background: <?= $i === $hienTai ? '#5a20cb' : '#fff' ?>; color: <?= $i === $hienTai ? '#fff' : '#333' ?>; text-decoration: none; border-radius: 4px;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </section>

    <!-- TAB 2: QUẢN LÝ LIÊN HỆ -->
    <?php else: ?>
        <section style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h3>Danh sách liên hệ từ khách hàng</h3>
            <?php if (empty($danhSachLienHe)): ?>
                <p>Chưa có thông tin liên hệ.</p>
            <?php else: ?>
                <?php foreach ($danhSachLienHe as $lienHe): ?>
                    <?php 
                        $hoTen = $lienHe['ten'] ?? $lienHe['hoTen'] ?? $lienHe['ho_ten'] ?? 'N/A';
                        $email = $lienHe['email'] ?? 'N/A';
                        $noiDung = $lienHe['noi_dung'] ?? $lienHe['noiDung'] ?? $lienHe['soThich'] ?? 'N/A';
                        $ngayTao = $lienHe['ngayTao'] ?? $lienHe['ngay_tao'] ?? $lienHe['thoiGian'] ?? 'N/A';
                    ?>
                    <article style="border-bottom: 2px solid #eee; padding: 15px 0; margin-bottom: 15px;">
                        <p><strong>Họ tên:</strong> <?= e((string)$hoTen) ?></p>
                        <p><strong>Email:</strong> <?= e((string)$email) ?></p>
                        <p><strong>Nội dung chi tiết:</strong></p>
                        <div style="background: #f9f9f9; padding: 10px; border-radius: 4px; margin: 5px 0; white-space: pre-wrap;"><?= nl2br(e((string)$noiDung)) ?></div>
                        <p style="font-size: 13px; color: #666; margin-top: 8px;"><strong>Thời gian gửi:</strong> <?= e((string)$ngayTao) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>