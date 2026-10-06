<?php

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

if (daDangNhap()) {
    redirect('quan-tri.php');
}

$loi = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $matKhau = (string) ($_POST['mat-khau'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi = 'Email không hợp lệ.';
    } elseif ($matKhau === '') {
        $loi = 'Vui lòng nhập mật khẩu.';
    } elseif (!dangNhap($email, $matKhau)) {
        $loi = 'Email hoặc mật khẩu không đúng.';

        error_log(
            'Đăng nhập thất bại: ' . $email
        );
    } else {
        redirect('quan-tri.php');
    }
}

$tieuDeTrang = 'Đăng nhập quản trị';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">

    <section>
        <h2>Đăng nhập quản trị</h2>

        <?php if ($loi !== ''): ?>
            <p role="alert">
                <?= e($loi) ?>
            </p>
        <?php endif; ?>

        <form method="post" action="dang-nhap.php">

            <div>
                <label for="email">Email:</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($email) ?>"
                    required>
            </div>

            <div>
                <label for="mat-khau">Mật khẩu:</label>

                <input
                    type="password"
                    id="mat-khau"
                    name="mat-khau"
                    required>
            </div>

            <button type="submit">
                Đăng nhập
            </button>

        </form>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>