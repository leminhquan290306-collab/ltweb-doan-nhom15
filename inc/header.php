<?php
$tieuDeTrang = $tieuDeTrang ?? 'Quà Tặng Thông Minh';
$baseUrl = $baseUrl ?? '';

$laTrangCaNhan = isset($trangCaNhan) && $trangCaNhan;
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

    <?= $cssCaNhan ?? '' ?>
</head>

<body class="trang<?= $laTrangCaNhan ? ' trang-ca-nhan-body' : '' ?>">

<header class="dau-trang">
    <div class="dau-trang-noi-dung">
        <h1>Quà Tặng Thông Minh</h1>

        <p>
            Gợi ý món quà phù hợp cho những người bạn yêu thương
        </p>

        <p>
            Yêu thích:
            <strong data-so-luong-yeu-thich>0</strong>
        </p>
    </div>

    <div class="dau-trang-tai-khoan">
        <?php
        require_once __DIR__ . '/tai-khoan.php';
        ?>

        <?php if (daDangNhap()): ?>
            <span class="ten-tai-khoan">
                Xin chào, <?= e($_SESSION['tai_khoan']['hoTen'] ?? $_SESSION['ho_ten'] ?? $_SESSION['tai_khoan']['email'] ?? $_SESSION['email'] ?? 'Bạn') ?>!
            </span>

            <!-- Thêm liên kết dẫn nhanh tới Thông tin cá nhân & Lịch sử mua hàng -->
            <a href="<?= e($baseUrl) ?>thong-tin-ca-nhan.php">Thông tin cá nhân</a>

            <?php if (laQuanTri()): ?>
                <a href="<?= e($baseUrl) ?>quan-tri.php">Quản trị</a>
            <?php endif; ?>

            <a href="<?= e($baseUrl) ?>dang-xuat.php">Đăng xuất</a>
        <?php else: ?>
            <a href="<?= e($baseUrl) ?>dang-nhap.php">Đăng nhập</a>
            <a href="<?= e($baseUrl) ?>dang-ky.php">Đăng ký</a>
        <?php endif; ?>
    </div>
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