<?php
declare(strict_types=1);

// Nạp các file cấu hình và thư viện tài khoản chuẩn của nhóm
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

// Kiểm tra nếu đã đăng nhập thì chuyển hướng luôn
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
    <section class="card" style="max-width: 500px; margin: 30px auto; padding: 20px;">
        <h2>Đăng nhập tài khoản</h2>

        <?php if ($loi !== ''): ?>
            <p role="alert" style="color: red; font-weight: bold;"><?= e($loi) ?></p>
        <?php endif; ?>

        <form method="post" action="dang-nhap.php">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(taoCsrfToken()) ?>"
            >

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
                <label for="mat-khau">Mật khẩu:</label><br>
                <input
                    type="password"
                    id="mat-khau"
                    name="mat-khau"
                    autocomplete="current-password"
                    required
                    style="width: 100%; padding: 8px;"
                >
            </p>

            <p>
                <button type="submit" class="btn btn--chinh" style="padding: 10px 20px;">Đăng nhập</button>
            </p>
        </form>

        <p style="margin-top: 15px;">
            Chưa có tài khoản?
            <a href="dang-ky.php">Đăng ký ngay</a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>