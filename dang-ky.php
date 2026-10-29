<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';
use App\Data\KhoNguoiDung;

$tieuDeTrang = 'Đăng ký tài khoản - Quà Tặng Thông Minh';
$baseUrl = '';

$hoTen = '';
$email = '';
$loi = '';
$thanhCong = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTen = trim((string) ($_POST['ho-ten'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $matKhau = (string) ($_POST['mat-khau'] ?? '');
    $xacNhanMatKhau = (string) ($_POST['xac-nhan-mat-khau'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!kiemTraCsrfToken(is_string($csrfToken) ? $csrfToken : null)) {
        $loi = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
    } elseif ($matKhau !== $xacNhanMatKhau) {
        $loi = 'Mật khẩu xác nhận không khớp.';
    } elseif (mb_strlen($matKhau) < 8) {
        $loi = 'Mật khẩu phải có ít nhất 8 ký tự.';
    } else {
        try {
            $khoNguoiDung = new KhoNguoiDung();
            
            // Mã hóa mật khẩu bảo mật
            $matKhauHash = password_hash($matKhau, PASSWORD_DEFAULT);

            // Lưu trực tiếp vào bảng nguoi_dung qua KhoNguoiDung OOP
            $khoNguoiDung->taoMoi($matKhauHash, $hoTen, $email);

            $thanhCong = true;
            $hoTen = '';
            $email = '';
        } catch (Throwable $e) {
            error_log((string) $e);
            $loi = 'Email này đã được đăng ký hoặc không thể đăng ký lúc này.';
        }
    }
}

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">
    <section class="card" style="max-width: 500px; margin: 30px auto; padding: 20px;">
        <h2>Đăng ký tài khoản khách hàng</h2>

        <?php if ($thanhCong): ?>
            <p role="status" style="color: green; font-weight: bold;">Đăng ký thành công! Bạn có thể đăng nhập bằng tài khoản vừa tạo.</p>
            <p><a href="dang-nhap.php" class="btn btn--chinh">Đến trang đăng nhập</a></p>
        <?php else: ?>

            <?php if ($loi !== ''): ?>
                <p role="alert" style="color: red;"><?= e($loi) ?></p>
            <?php endif; ?>

            <form method="post" action="dang-ky.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(taoCsrfToken()) ?>"
                >

                <p>
                    <label for="ho-ten">Họ và tên:</label><br>
                    <input
                        type="text"
                        id="ho-ten"
                        name="ho-ten"
                        maxlength="100"
                        value="<?= e($hoTen) ?>"
                        autocomplete="name"
                        required
                        style="width: 100%; padding: 8px;"
                    >
                </p>

                <p>
                    <label for="email">Email:</label><br>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($email) ?>"
                        autocomplete="email"
                        required
                        style="width: 100%; padding: 8px;"
                    >
                </p>

                <p>
                    <label for="mat-khau">Mật khẩu (ít nhất 8 ký tự):</label><br>
                    <input
                        type="password"
                        id="mat-khau"
                        name="mat-khau"
                        minlength="8"
                        autocomplete="new-password"
                        required
                        style="width: 100%; padding: 8px;"
                    >
                </p>

                <p>
                    <label for="xac-nhan-mat-khau">Xác nhận mật khẩu:</label><br>
                    <input
                        type="password"
                        id="xac-nhan-mat-khau"
                        name="xac-nhan-mat-khau"
                        minlength="8"
                        autocomplete="new-password"
                        required
                        style="width: 100%; padding: 8px;"
                    >
                </p>

                <p>
                    <button type="submit" class="btn btn--chinh" style="padding: 10px 20px;">Đăng ký</button>
                </p>
            </form>

            <p style="margin-top: 15px;">Đã có tài khoản? <a href="dang-nhap.php">Đăng nhập ngay</a></p>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>