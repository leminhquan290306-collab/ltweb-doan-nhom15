<?php

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

use App\Data\KhoLienHe;

if (!laQuanTri()) {
    redirect('dang-nhap.php');
}

$khoLienHe = new KhoLienHe();
$danhSachLienHe = $khoLienHe->layTatCa();

$tieuDeTrang = 'Quản trị - Danh sách liên hệ';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">

    <section>
        <h2>Trang quản trị</h2>

        <p>
            Xin chào:
            <strong><?= e($_SESSION['tai_khoan']['email']) ?></strong>
        </p>

        <p>
            Tổng số liên hệ:
            <strong><?= count($danhSachLienHe) ?></strong>
        </p>

        <p>
            <a href="dang-xuat.php">Đăng xuất</a>
        </p>
    </section>

    <section>
        <h2>Danh sách liên hệ</h2>

        <?php if (empty($danhSachLienHe)): ?>

            <p>Chưa có thông tin liên hệ.</p>

        <?php else: ?>

            <?php foreach ($danhSachLienHe as $lienHe): ?>

                <article>
                    <hr>

                    <p>
                        <strong>Họ tên:</strong>
                        <?= e($lienHe['hoTen'] ?? '') ?>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <?= e($lienHe['email'] ?? '') ?>
                    </p>

                    <p>
                        <strong>Số điện thoại:</strong>
                        <?= e($lienHe['soDienThoai'] ?? '') ?>
                    </p>

                    <p>
                        <strong>Người nhận:</strong>
                        <?= e($lienHe['nguoiNhan'] ?? '') ?>
                    </p>

                    <p>
                        <strong>Dịp tặng:</strong>
                        <?= e($lienHe['dipTang'] ?? '') ?>
                    </p>

                    <p>
                        <strong>Ngân sách:</strong>
                        <?= e($lienHe['nganSach'] ?? '') ?> đ
                    </p>

                    <p>
                        <strong>Ngày cần quà:</strong>
                        <?= e($lienHe['ngayCanQua'] ?? '') ?>
                    </p>

                    <p>
                        <strong>Sở thích:</strong>
                        <?= e($lienHe['soThich'] ?? '') ?>
                    </p>

                    <?php if (!empty($lienHe['anh'])): ?>
                        <p>
                            <strong>Ảnh đính kèm:</strong>
                            <?= e($lienHe['anh']) ?>
                        </p>
                    <?php endif; ?>

                    <p>
                        <strong>Thời gian:</strong>
                        <?= e($lienHe['thoiGian'] ?? '') ?>
                    </p>

                </article>

            <?php endforeach; ?>

        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>