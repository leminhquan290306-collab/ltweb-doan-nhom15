<?php

declare(strict_types=1);

require_once __DIR__ . '/inc/ham.php';

http_response_code(500);

$tieuDeTrang = '500 - Lỗi máy chủ';
$baseUrl = '';

require_once __DIR__ . '/inc/header.php';
?>

<main class="container">
    <section>
        <h2>500 - Đã xảy ra lỗi máy chủ</h2>
        <p>Xin lỗi, hệ thống đang gặp sự cố. Vui lòng thử lại sau.</p>
        <p>
            <a href="<?= e($baseUrl) ?>index.php">
                Quay về trang chủ
            </a>
        </p>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>