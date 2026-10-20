<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

$tieuDeTrang = 'Đăng ký tài khoản';
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
    } else {
        try {
            $loi = dangKyTaiKhoan($hoTen, $email, $matKhau);

            if ($loi === null) {
                $thanhCong = true;
                $hoTen = '';
                $email = '';
            }
        } catch (Throwable $e) {
            error_log((string) $e);
            $loi = 'Không thể đăng ký lúc này. Vui lòng thử lại sau.';
        }
    }
}

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">
    <section>
        <h2>Đăng ký tài khoản khách hàng</h2>

        <?php if ($thanhCong): ?>
            <p role="status">Đăng ký thành công! Bà có thể đăng nhập bằng tài khoản vừa tạo.</p>
            <p><a href="dang-nhap.php">Đến trang đăng nhập</a></p>
        <?php else: ?>

            <?php if ($loi !== ''): ?>
                <p role="alert"><?= e($loi) ?></p>
            <?php endif; ?>

            <form method="post" action="dang-ky.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(taoCsrfToken()) ?>"
                >

                <div>
                    <label for="ho-ten">Họ và tên:</label>
                    <input
                        type="text"
                        id="ho-ten"
                        name="ho-ten"
                        maxlength="100"
                        value="<?= e($hoTen) ?>"
                        autocomplete="name"
                        required
                    >
                </div>

                <div>
                    <label for="email">Email:</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($email) ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <div>
                    <label for="mat-khau">Mật khẩu (ít nhất 8 ký tự):</label>
                    <input
                        type="password"
                        id="mat-khau"
                        name="mat-khau"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div>
                    <label for="xac-nhan-mat-khau">Xác nhận mật khẩu:</label>
                    <input
                        type="password"
                        id="xac-nhan-mat-khau"
                        name="xac-nhan-mat-khau"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit">Đăng ký</button>
            </form>

            <p>Đã có tài khoản? <a href="dang-nhap.php">Đăng nhập</a></p>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
