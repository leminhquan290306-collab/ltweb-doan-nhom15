<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

http_response_code(500);
$tieuDeTrang = '500 - Lỗi máy chủ';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tieuDeTrang, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="css/01-bien.css">
    <link rel="stylesheet" href="css/02-chuan-hoa.css">
    <link rel="stylesheet" href="css/03-bo-cuc.css">
    <link rel="stylesheet" href="css/04-thanh-phan.css">
</head>
<body style="font-family: sans-serif; background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
    <main style="max-width: 500px; width: 100%; padding: 20px; text-align: center;">
        <section class="card" style="background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <h2 style="color: #e74c3c; margin-top: 0;">500 - Lỗi máy chủ</h2>
            <p>Xin lỗi, hệ thống đang gặp sự cố kỹ thuật hoặc kết nối cơ sở dữ liệu bị lỗi. Vui lòng thử lại sau.</p>
            <p style="margin-top: 20px;">
                <a href="index.php" style="background: #3498db; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                    Quay về trang chủ
                </a>
            </p>
        </section>
    </main>
</body>
</html>