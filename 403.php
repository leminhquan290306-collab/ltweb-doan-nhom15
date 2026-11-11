<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

http_response_code(403);
$tieuDeTrang = '403 - Không có quyền truy cập';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container trang-403">
    <section class="card trang-403__card">
        <h2 class="trang-403__tieu-de">403 - Truy cập bị từ chối</h2>
        <p>Rất tiếc, bạn không có quyền hạn (hoặc vai trò quản trị) để truy cập vào trang này.</p>
        <p>Vui lòng đăng nhập bằng tài khoản có quyền phù hợp hoặc quay lại trang chủ.</p>

        <p class="trang-403__lien-ket">
            <a href="<?= e($baseUrl) ?>index.php" class="btn btn--chinh trang-403__nut">
                Quay về trang chủ
            </a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
