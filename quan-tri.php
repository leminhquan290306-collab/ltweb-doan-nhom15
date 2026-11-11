<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/xac-thuc.php';

// Kiểm tra quyền quản trị viên tối cao
yeuCauQuyen('quan_tri');

use App\Data\KhoSanPham;

$khoSanPham = new KhoSanPham(db());

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

// Lấy danh sách liên hệ từ cơ sở dữ liệu
$danhSachLienHe = [];
try {
    $pdo = db();
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

<main class="container quan-tri-container">

    <!-- HEADER DASHBOARD -->
    <section class="quan-tri-header">
        <div>
            <h2>Khu vực Quản trị (Admin)</h2>
            <?php $nd = nguoiDung(); ?>
            <p>Xin chào: <strong><?= e($nd['ho_ten'] ?? $nd['email'] ?? 'Admin') ?></strong></p>
        </div>
        <div>
            <a href="quan-tri.php?tab=san-pham" class="btn quan-tri-tab <?= $tab === 'san-pham' ? 'dang-chon' : '' ?>">Quản lý Sản phẩm</a>
            <a href="quan-tri.php?tab=lien-he" class="btn quan-tri-tab quan-tri-tab-lien-he <?= $tab === 'lien-he' ? 'dang-chon' : '' ?>">Quản lý Liên hệ (<?= count($danhSachLienHe) ?>)</a>
            <<form method="POST" action="dang-xuat.php" class="form-dang-xuat-quan-tri">
    <?= csrfField() ?>
    <button type="submit" class="nut-dang-xuat-quan-tri">
        Đăng xuất
    </button>
</form>
        </div>
    </section>

    <?php if ($thongBao !== ''): ?>
        <p class="quan-tri-thong-bao"><?= e($thongBao) ?></p>
    <?php endif; ?>

    <!-- TAB 1: QUẢN LÝ SẢN PHẨM -->
    <?php if ($tab === 'san-pham'): ?>
        <section class="quan-tri-khung">
            <div class="quan-tri-tieu-de">
                <h3 class="quan-tri-tieu-de-chinh">Danh sách sản phẩm</h3>
                <a href="them-san-pham.php" class="nut-them-san-pham">+ Thêm sản phẩm mới</a>
            </div>

            <div class="quan-tri-bang-cuon">
                <table class="quan-tri-bang">
                    <thead>
                        <tr class="quan-tri-bang-dau">
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th class="quan-tri-can-giua">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($danhSachSanPham)): ?>
                            <tr><td colspan="7" class="quan-tri-bang-trong">Chưa có sản phẩm nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($danhSachSanPham as $sp): ?>
                                <tr class="quan-tri-bang-dong">
                                    <td><?= e((string)$sp->getId()) ?></td>
                                    <td>
                                    <img src="<?= e($sp->getHinhAnh()) ?>" alt="" class="quan-tri-anh-san-pham">
                                    </td>
                                    <td class="quan-tri-ten-san-pham"><?= e($sp->getTen()) ?></td>
                                    <td><?= e($sp->getDanhMuc()) ?></td>
                                    <td class="quan-tri-gia-san-pham"><?= number_format($sp->getGia(), 0, ',', '.') ?> đ</td>
                                    <td><?= e((string)$sp->getSoLuong()) ?></td>
                                    <td class="quan-tri-can-giua">
                                        <a href="sua-san-pham.php?id=<?= e((string)$sp->getId()) ?>" class="nut-sua-san-pham">Sửa</a>
                                        <form action="xoa-san-pham.php" method="POST" class="form-xoa-san-pham">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= e((string)$sp->getId()) ?>">
    <button type="submit" class="nut-xoa-san-pham">
        Xóa
    </button>
</form>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang quản trị -->
            <?php if ($tongSoTrang > 1): ?>
                <nav class="quan-tri-phan-trang">
                    <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
                        <a href="quan-tri.php?tab=san-pham&trang=<?= $i ?>" class="<?= $i === $hienTai ? 'dang-chon' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </section>

    <!-- TAB 2: QUẢN LÝ LIÊN HỆ -->
    <?php else: ?>
        <section class="quan-tri-khung">
            <h3>Danh sách liên hệ từ khách hàng</h3>
            <?php if (empty($danhSachLienHe)): ?>
                <p>Chưa có thông tin liên hệ.</p>
            <?php else: ?>
                <?php foreach ($danhSachLienHe as $lienHe): ?>
                    <?php 
                        $hoTen = $lienHe['ten'] ?? $lienHe['ho_ten'] ?? 'N/A';
                        $email = $lienHe['email'] ?? 'N/A';
                        $noiDung = $lienHe['noi_dung'] ?? $lienHe['noiDung'] ?? 'N/A';
                        $ngayTao = $lienHe['ngay_tao'] ?? $lienHe['ngayTao'] ?? 'N/A';
                    ?>
                    <article class="quan-tri-lien-he">
                        <p><strong>Họ tên:</strong> <?= e((string)$hoTen) ?></p>
                        <p><strong>Email:</strong> <?= e((string)$email) ?></p>
                        <p><strong>Nội dung chi tiết:</strong></p>
                        <div class="quan-tri-noi-dung-lien-he"><?= nl2br(e((string)$noiDung)) ?></div>
                        <p class="quan-tri-thoi-gian"><strong>Thời gian gửi:</strong> <?= e((string)$ngayTao) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>