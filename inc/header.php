<?php
$tieuDeTrang = $tieuDeTrang ?? 'Quà Tặng Thông Minh';
$baseUrl = $baseUrl ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($tieuDeTrang) ?></title>

    <meta name="description"
          content="Website tư vấn và mua sắm quà tặng thông minh, giúp người dùng tìm món quà phù hợp với người nhận, dịp tặng và ngân sách.">

    <link rel="icon"
          type="image/jpeg"
          href="<?= e($baseUrl) ?>images/hop-qua.jpg">

    <link rel="stylesheet" href="<?= e($baseUrl) ?>css/01-bien.css">
    <link rel="stylesheet" href="<?= e($baseUrl) ?>css/02-chuan-hoa.css">
    <link rel="stylesheet" href="<?= e($baseUrl) ?>css/03-bo-cuc.css">
    <link rel="stylesheet" href="<?= e($baseUrl) ?>css/04-thanh-phan.css">
    <link rel="stylesheet" href="<?= e($baseUrl) ?>css/05-tien-ich.css">
</head>

<body class="trang">

<header>
    <h1>Quà Tặng Thông Minh</h1>

    <p>
        Gợi ý món quà phù hợp cho những người bạn yêu thương
    </p>

    <p>
        Yêu thích:
        <strong data-so-luong-yeu-thich>0</strong>
    </p>
</header>

<nav aria-label="Điều hướng chính">

    <button
        class="nut-menu"
        type="button"
        aria-expanded="false"
        aria-controls="menu-chinh">
        Menu
    </button>

    <ul class="menu-list" id="menu-chinh">

        <li>
            <a href="<?= e($baseUrl) ?>index.php">Trang chủ</a>
        </li>

        <li>
            <a href="<?= e($baseUrl) ?>danh-sach.php">Danh sách</a>
        </li>

        <li>
            <a href="<?= e($baseUrl) ?>chi-tiet.php">Chi tiết</a>
        </li>

        <li>
            <a href="<?= e($baseUrl) ?>gioi-thieu.php">Về chúng tôi</a>
        </li>

        <li>
            <a href="<?= e($baseUrl) ?>lien-he.php">Liên hệ</a>
        </li>

    </ul>

</nav>
