<?php
declare(strict_types=1);

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
    <link rel="stylesheet" href="css/05-tien-ich.css">
</head>
<body class="trang-500">
    <main class="trang-500__main">
        <section class="card trang-500__card">
            <h2 class="trang-500__tieu-de">500 - Lỗi máy chủ</h2>
            <p>Xin lỗi, hệ thống đang gặp sự cố kỹ thuật hoặc kết nối cơ sở dữ liệu bị lỗi. Vui lòng thử lại sau.</p>
            <p class="trang-500__lien-ket">
                <a href="index.php" class="trang-500__nut">
                    Quay về trang chủ
                </a>
            </p>
        </section>
    </main>
</body>
</html>
