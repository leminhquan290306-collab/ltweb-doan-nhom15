
<?php
require __DIR__ . '/../../inc/config.php';

$baseUrl = '../../';
$trangCaNhan = true;
$tieuDeTrang = 'Giới thiệu cá nhân - Lê Minh Quân';
$cssCaNhan = '<link rel="stylesheet" href="style.css?v=99">';

/* 1. Dữ liệu kỹ năng */
$kyNang = [
    [
        'ten' => 'Lập trình Web',
        'nhom' => 'web',
        'mo_ta' => 'HTML5, CSS3, PHP 8 và JavaScript.'
    ],
    [
        'ten' => 'Cơ sở dữ liệu',
        'nhom' => 'laptrinh',
        'mo_ta' => 'Thực hành làm việc với MySQL và PostgreSQL.'
    ],
    [
        'ten' => 'Lập trình cơ bản',
        'nhom' => 'laptrinh',
        'mo_ta' => 'Sử dụng Python và C++ để giải quyết các bài toán.'
    ],
    [
        'ten' => 'Công cụ phát triển',
        'nhom' => 'congcu',
        'mo_ta' => 'Git, GitHub và Visual Studio Code.'
    ]
];

/* 2. Dữ liệu dự án */
$duAn = [
    [
        'ten' => 'Website Quà Tặng Thông Minh',
        'nhom' => 'web',
        'mo_ta' => 'Xây dựng website giới thiệu và hiển thị danh sách sản phẩm quà tặng.'
    ],
    [
        'ten' => 'Trang giới thiệu cá nhân',
        'nhom' => 'web',
        'mo_ta' => 'Thiết kế trang cá nhân bằng HTML, CSS và PHP.'
    ],
    [
        'ten' => 'Bài tập lập trình mạng',
        'nhom' => 'laptrinh',
        'mo_ta' => 'Thực hành giao tiếp giữa client và server bằng socket.'
    ]
];

/* 3. Lọc kỹ năng và dự án theo nhóm bằng GET */
$tenNhom = [
    'tatca' => 'Tất cả',
    'web' => 'Phát triển Web',
    'laptrinh' => 'Lập trình',
    'congcu' => 'Công cụ'
];

$nhom = isset($_GET['nhom']) && is_string($_GET['nhom'])
    ? $_GET['nhom']
    : 'tatca';

if (!array_key_exists($nhom, $tenNhom)) {
    $nhom = 'tatca';
}

$kyNangHienThi = array_filter(
    $kyNang,
    function ($item) use ($nhom) {
        return $nhom === 'tatca' || $item['nhom'] === $nhom;
    }
);

$duAnHienThi = array_filter(
    $duAn,
    function ($item) use ($nhom) {
        return $nhom === 'tatca' || $item['nhom'] === $nhom;
    }
);

require __DIR__ . '/../../inc/header.php';
?>

<div class="trang-ca-nhan">
    <main>

        <!-- Tiêu đề trang -->
        <h1 class="tieu-de-trang">Trang Giới Thiệu Cá Nhân</h1>

        <!-- 1. Khối thông tin cá nhân -->
        <div class="khoi-dau thong-tin-ca-nhan">
            <img
                class="anh-chan-dung"
                src="anh_the.jpg"
                alt="Ảnh chân dung Lê Minh Quân"
                width="150"
                height="150"
            >

            <div class="thong-tin-chinh">
                <p>
                    <strong>Họ và tên:</strong> Lê Minh Quân
                </p>

                <p>
                    <strong>Lớp:</strong> 24CNTT2
                </p>

                <p>
                    <strong>MSSV:</strong> 3120224116
                </p>

                <p>
                    <strong>Sở thích:</strong> Bóng đá, đọc sách và lập trình web.
                </p>

                <p>
                    Tôi luôn cố gắng học hỏi kiến thức mới, rèn luyện kỹ năng
                    và vận dụng những gì đã học vào các bài tập, dự án.
                </p>
            </div>
        </div>

        <!-- 2. Đóng góp trong đồ án nhóm -->
        <section class="muc-noi-dung">
            <h2>Đóng góp trong đồ án nhóm</h2>

            <p>
                Tham gia xây dựng giao diện website, tổ chức mã nguồn,
                chỉnh sửa HTML, CSS và PHP, kiểm tra hoạt động của trang
                web và quản lý mã nguồn bằng GitHub.
            </p>
        </section>

        <!-- 3. Kỹ năng và dự án học tập -->
        <section class="muc-noi-dung">
            <h2>Kỹ năng và dự án học tập</h2>

            <p>
                Dưới đây là những kỹ năng và dự án mà tôi đã thực hành
                trong quá trình học tập.
            </p>

            <form method="get" action="gioithieu.php" class="form-loc">
                <label for="nhom">Lọc theo nhóm:</label>

                <select id="nhom" name="nhom">
                    <?php foreach ($tenNhom as $maNhom => $nhanNhom): ?>
                        <option
                            value="<?= e($maNhom) ?>"
                            <?= $nhom === $maNhom ? 'selected' : '' ?>
                        >
                            <?= e($nhanNhom) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Lọc dữ liệu</button>
            </form>

            <p>
                <strong>Nhóm đang xem:</strong>
                <?= e($tenNhom[$nhom]) ?>
            </p>

            <h3>Danh sách kỹ năng</h3>

            <?php if (count($kyNangHienThi) > 0): ?>
                <div class="danh-sach-ky-nang">
                    <?php foreach ($kyNangHienThi as $item): ?>
                        <article class="the-ky-nang">
                            <h3><?= e($item['ten']) ?></h3>
                            <p><?= e($item['mo_ta']) ?></p>
                            <p>
                                <strong>Nhóm:</strong>
                                <?= e($tenNhom[$item['nhom']]) ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Không có kỹ năng trong nhóm này.</p>
            <?php endif; ?>

            <h3>Danh sách dự án</h3>

            <?php if (count($duAnHienThi) > 0): ?>
                <div class="danh-sach-du-an">
                    <?php foreach ($duAnHienThi as $item): ?>
                        <article class="the-du-an">
                            <h3><?= e($item['ten']) ?></h3>
                            <p><?= e($item['mo_ta']) ?></p>
                            <p>
                                <strong>Nhóm:</strong>
                                <?= e($tenNhom[$item['nhom']]) ?>
                            </p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Không có dự án trong nhóm này.</p>
            <?php endif; ?>
        </section>

        <!-- 4. Sở thích cá nhân -->
        <section class="muc-noi-dung">
            <h2>Sở thích cá nhân</h2>

            <ul>
                <li>Bóng đá</li>
                <li>Đọc sách</li>
                <li>Lập trình web</li>
            </ul>
        </section>

        <!-- 5. Chuyển giao diện sáng/tối -->
        <section class="muc-noi-dung">
            <h2>Tùy chỉnh giao diện</h2>

            <p>
                Bạn có thể chuyển đổi giữa giao diện sáng và giao diện tối
                để phù hợp với nhu cầu sử dụng.
            </p>

            <button
                type="button"
                id="nut-doi-giao-dien"
                aria-pressed="false"
            >
                🌙 Chuyển sang giao diện tối
            </button>
        </section>

    </main>
</div>

<script src="js/canhan.js?v=5" defer></script>

<?php require __DIR__ . '/../../inc/footer.php'; ?>
