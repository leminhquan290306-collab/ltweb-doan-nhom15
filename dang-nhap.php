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
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    if (!kiemTraCsrfToken(is_string($csrfToken) ? $csrfToken : null)) {
        $loi = 'Phiên gửi biểu mẫu không hợp lệ. Vui lòng tải lại trang.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = 'Email hoặc mật khẩu không đúng.';
    } elseif ($matKhau === '') {
        $loi = 'Email hoặc mật khẩu không đúng.';
    } else {
        try {
            $soLanSai = demLanDangNhapThatBai($ip);

            if ($soLanSai >= 5) {
                http_response_code(429);
                $loi = 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.';
            } else {
                $ketQuaDangNhap = dangNhapKetQua($email, $matKhau);

                if ($ketQuaDangNhap === true) {
                    if (laQuanTri()) {
                        redirect('quan-tri.php');
                    }

                    redirect('index.php');
                } elseif ($ketQuaDangNhap === false) {
                    $soLanSai = ghiNhanDangNhapThatBai($ip);

                    ghiNhatKyBaoMat('dang_nhap_sai', [
                        'ip' => $ip,
                        'email_hash' => hash('sha256', strtolower($email)),
                    ]);

                    if ($soLanSai >= 5) {
                        http_response_code(429);
                        $loi = 'Bạn đã đăng nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.';
                    } else {
                        $loi = 'Email hoặc mật khẩu không đúng.';
                    }
                } else {
                    http_response_code(503);
                    $loi = 'Hệ thống đang gặp sự cố. Vui lòng thử lại sau.';
                }
            }
        } catch (Throwable $e) {
            error_log('Lỗi xử lý giới hạn đăng nhập.');
            http_response_code(503);
            $loi = 'Hệ thống đang gặp sự cố. Vui lòng thử lại sau.';
        }
    }
}

$tieuDeTrang = 'Đăng nhập tài khoản';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>


<main class="container">
    <section class="card dang-nhap-khung">
        <h2>Đăng nhập tài khoản</h2>

        <?php if ($loi !== ''): ?>
            <p role="alert" class="dang-nhap-loi"><?= e($loi) ?></p>
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
                    class="dang-nhap-input"
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
                    class="dang-nhap-input"
                >
            </p>

            <p>
                <button type="submit" class="btn btn--chinh nut-dang-nhap">
                    Đăng nhập
                </button>
            </p>
        </form>

        <p class="dang-nhap-dang-ky">
            Chưa có tài khoản?
            <a href="dang-ky.php">Đăng ký ngay</a>
        </p>
    </section>
</main>


<?php require_once __DIR__ . '/inc/footer.php'; ?>