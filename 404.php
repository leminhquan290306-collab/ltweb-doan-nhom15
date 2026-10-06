<?php

require_once __DIR__ . '/inc/config.php';

http_response_code(404);

$tieuDeTrang = '404 - Không tìm thấy trang';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">
    <section>
        <h2>404 - Không tìm thấy trang</h2>

        <p>
            Xin lỗi, trang bạn đang tìm kiếm không tồn tại
            hoặc đã được chuyển sang địa chỉ khác.
        </p>

        <p>
            <a href="<?= e($baseUrl) ?>index.php">
                Quay về trang chủ
            </a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>