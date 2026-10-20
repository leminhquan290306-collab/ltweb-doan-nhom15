<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

if (daDangNhap()) {
    if (laQuanTri()) {
        redirect('quan-tri.php');
    }

    redirect('index.php');
}

$loi = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $matKhau = (string) ($_POST['mat-khau'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!kiemTraCsrfToken(is_string($csrfToken) ? $csrfToken : null)) {
        $loi = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = 'Email không hợp lệ.';
    } elseif ($matKhau === '') {
        $loi = 'Vui lòng nhập mật khẩu.';
    } elseif (!dangNhap($email, $matKhau)) {
        $loi = 'Email hoặc mật khẩu không đúng.';
        error_log('Đăng nhập thất bại: ' . $email);
    } else {
        if (laQuanTri()) {
            redirect('quan-tri.php');
        }

        redirect('index.php');
    }
}

$tieuDeTrang = 'Đăng nhập tài khoản';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">
    <section>
        <h2>Đăng nhập tài khoản</h2>

        <?php if ($loi !== ''): ?>
            <p role="alert"><?= e($loi) ?></p>
        <?php endif; ?>

        <form method="post" action="dang-nhap.php">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(taoCsrfToken()) ?>"
            >

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
                <label for="mat-khau">Mật khẩu:</label>
                <input
                    type="password"
                    id="mat-khau"
                    name="mat-khau"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit">Đăng nhập</button>
        </form>

        <p>
            Chưa có tài khoản?
            <a href="dang-ky.php">Đăng ký ngay</a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
