<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

use App\Data\KhoLienHe;

if (!laQuanTri()) {
    redirect('dang-nhap.php');
}

$danhSachLienHe = [];
if (class_exists(KhoLienHe::class)) {
    try {
        $khoLienHe = new KhoLienHe();
        $danhSachLienHe = $khoLienHe->layTatCa();
    } catch (Throwable $e) {
        $danhSachLienHe = [];
    }
}

if (empty($danhSachLienHe)) {
    try {
        $pdo = hamCSDL();
        $stmt = $pdo->query("SELECT * FROM lien_he ORDER BY id DESC");
        $danhSachLienHe = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        $danhSachLienHe = [];
    }
}

$tieuDeTrang = 'Quản trị - Danh sách liên hệ';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 900px; margin: 30px auto; padding: 0 15px;">

    <section style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h2>Trang quản trị</h2>
        <p>Xin chào: <strong><?= e($_SESSION['email'] ?? $_SESSION['tai_khoan']['email'] ?? 'Admin') ?></strong></p>
        <p>Tổng số liên hệ: <strong><?= count($danhSachLienHe) ?></strong></p>
        <p><a href="dang-xuat.php" style="color: #dc3545; text-decoration: none; font-weight: bold;">Đăng xuất</a></p>
    </section>

    <section style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2>Danh sách liên hệ</h2>

        <?php if (empty($danhSachLienHe)): ?>
            <p>Chưa có thông tin liên hệ.</p>
        <?php else: ?>
            <?php foreach ($danhSachLienHe as $lienHe): ?>
                <?php 
                    // Lấy chính xác các tên cột trong CSDL: ten, email, noi_dung, ngayTao
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

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>