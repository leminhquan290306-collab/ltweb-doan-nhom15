<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';

http_response_code(403);
$tieuDeTrang = '403 - Không có quyền truy cập';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 600px; margin: 40px auto; text-align: center;">
    <section class="card" style="padding: 30px;">
        <h2 style="color: #e74c3c; margin-top: 0;">403 - Truy cập bị từ chối</h2>
        <p>Rất tiếc, bạn không có quyền hạn (hoặc vai trò quản trị) để truy cập vào trang này.</p>
        <p>Vui lòng đăng nhập bằng tài khoản có quyền phù hợp hoặc quay lại trang chủ.</p>
        
        <p style="margin-top: 25px;">
            <a href="<?= e($baseUrl) ?>index.php" class="btn btn--chinh" style="background: #3498db; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Quay về trang chủ
            </a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>