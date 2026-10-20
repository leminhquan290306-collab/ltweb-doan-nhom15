
<?php
/*

* Tệp gioithieu.php hiển thị trang giới thiệu cá nhân của Lê Minh Quân.
* Trang sử dụng PHP để hiển thị nội dung giới thiệu và thông tin cá nhân.
* Chức năng lọc dữ liệu cho phép lọc nội dung theo điều kiện được chọn.
* Chức năng sáng/tối cho phép người dùng thay đổi giao diện trang web.
* Cách thử: mở trang giới thiệu, thử lọc dữ liệu và chuyển đổi sáng/tối.
  */

require __DIR__ . '/../../inc/config.php';

$baseUrl = '../../';
$trangCaNhan = true;
$tieuDeTrang = 'Giới thiệu cá nhân - Lê Minh Quân';
$cssCaNhan = '<link rel="stylesheet" href="style.css?v=99">';


/* 1. Kết nối cơ sở dữ liệu */
require_once __DIR__ . '/../../inc/db.php';

$pdo = hamCSDL();
$mssv = '3120224116';

/* 2. Danh sách nhóm dùng cho bộ lọc */
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

/* 3. Lấy kỹ năng từ MySQL */
$sqlKyNang = '
    SELECT ten_ky_nang AS ten, nhom, mo_ta
    FROM tv_ky_nang
    WHERE mssv = :mssv
';

$paramsKyNang = [':mssv' => $mssv];

if ($nhom !== 'tatca') {
    $sqlKyNang .= ' AND nhom = :nhom';
    $paramsKyNang[':nhom'] = $nhom;
}

$sqlKyNang .= ' ORDER BY id ASC';

$stmtKyNang = $pdo->prepare($sqlKyNang);
$stmtKyNang->execute($paramsKyNang);
$kyNangHienThi = $stmtKyNang->fetchAll();

/* 4. Lấy dự án từ MySQL */
$sqlDuAn = '
    SELECT ten_du_an AS ten, nhom, mo_ta
    FROM tv_du_an
    WHERE mssv = :mssv
';

$paramsDuAn = [':mssv' => $mssv];

if ($nhom !== 'tatca') {
    $sqlDuAn .= ' AND nhom = :nhom';
    $paramsDuAn[':nhom'] = $nhom;
}

$sqlDuAn .= ' ORDER BY id ASC';

$stmtDuAn = $pdo->prepare($sqlDuAn);
$stmtDuAn->execute($paramsDuAn);
$duAnHienThi = $stmtDuAn->fetchAll();

/* 5. Nhật ký học tập của Quân */
$thongBaoNhatKy = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['hanh_dong'] ?? '') === 'them_nhat_ky'
) {
    $tieuDeNhatKy = trim(
        is_string($_POST['tieu_de'] ?? null)
            ? $_POST['tieu_de']
            : ''
    );

    $noiDungNhatKy = trim(
        is_string($_POST['noi_dung'] ?? null)
            ? $_POST['noi_dung']
            : ''
    );

    if (
        $tieuDeNhatKy === ''
        || mb_strlen($tieuDeNhatKy) > 200
        || $noiDungNhatKy === ''
    ) {
        $_SESSION['thong_bao_nhat_ky'] =
            'Vui lòng nhập tiêu đề và nội dung hợp lệ.';
    } else {
        $stmtThemNhatKy = $pdo->prepare(
            'INSERT INTO tv_nhat_ky (mssv, tieu_de, noi_dung)
             VALUES (:mssv, :tieu_de, :noi_dung)'
        );

        $stmtThemNhatKy->execute([
            ':mssv' => $mssv,
            ':tieu_de' => $tieuDeNhatKy,
            ':noi_dung' => $noiDungNhatKy,
        ]);

        $_SESSION['thong_bao_nhat_ky'] =
            'Đã lưu nhật ký học tập thành công.';
    }

    header('Location: gioithieu.php#nhat-ky');
    exit;
}

if (isset($_SESSION['thong_bao_nhat_ky'])) {
    $thongBaoNhatKy = $_SESSION['thong_bao_nhat_ky'];
    unset($_SESSION['thong_bao_nhat_ky']);
}

$stmtNhatKy = $pdo->prepare(
    'SELECT id, tieu_de, noi_dung, ngay_tao
     FROM tv_nhat_ky
     WHERE mssv = :mssv
     ORDER BY id DESC
     LIMIT 10'
);

$stmtNhatKy->execute([':mssv' => $mssv]);
$nhatKyHienThi = $stmtNhatKy->fetchAll();
/* 6. Danh sách công việc của Quân */
$thongBaoCongViec = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['hanh_dong'] ?? '') === 'them_cong_viec'
) {
    $tenCongViec = trim(
        is_string($_POST['ten_cong_viec'] ?? null)
            ? $_POST['ten_cong_viec']
            : ''
    );

    if ($tenCongViec === '' || mb_strlen($tenCongViec) > 200) {
        $_SESSION['thong_bao_cong_viec'] =
            'Vui lòng nhập tên công việc hợp lệ.';
    } else {
        $stmtThemCongViec = $pdo->prepare(
            'INSERT INTO tv_cong_viec (mssv, ten_cong_viec)
             VALUES (:mssv, :ten_cong_viec)'
        );

        $stmtThemCongViec->execute([
            ':mssv' => $mssv,
            ':ten_cong_viec' => $tenCongViec,
        ]);

        $_SESSION['thong_bao_cong_viec'] =
            'Đã thêm công việc thành công.';
    }

    header('Location: gioithieu.php#cong-viec');
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['hanh_dong'] ?? '') === 'hoan_thanh_cong_viec'
) {
    $idCongViec = filter_var(
        $_POST['id_cong_viec'] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($idCongViec !== false && $idCongViec !== null) {
        $stmtHoanThanh = $pdo->prepare(
            'UPDATE tv_cong_viec
             SET hoan_thanh = 1
             WHERE id = :id
               AND mssv = :mssv
               AND hoan_thanh = 0'
        );

        $stmtHoanThanh->execute([
            ':id' => $idCongViec,
            ':mssv' => $mssv,
        ]);

        $_SESSION['thong_bao_cong_viec'] =
            $stmtHoanThanh->rowCount() > 0
                ? 'Đã đánh dấu công việc hoàn thành.'
                : 'Không tìm thấy công việc cần cập nhật.';
    } else {
        $_SESSION['thong_bao_cong_viec'] =
            'Mã công việc không hợp lệ.';
    }

    header('Location: gioithieu.php#cong-viec');
    exit;
}

if (isset($_SESSION['thong_bao_cong_viec'])) {
    $thongBaoCongViec = $_SESSION['thong_bao_cong_viec'];
    unset($_SESSION['thong_bao_cong_viec']);
}

$stmtCongViec = $pdo->prepare(
    'SELECT id, ten_cong_viec, hoan_thanh, ngay_tao
     FROM tv_cong_viec
     WHERE mssv = :mssv
     ORDER BY hoan_thanh ASC, id DESC
     LIMIT 20'
);

$stmtCongViec->execute([':mssv' => $mssv]);
$congViecHienThi = $stmtCongViec->fetchAll();



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
                                <?= e($tenNhom[$item['nhom']] ?? 'Chưa phân nhóm') ?>
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
    

        <!-- 4. Nhật ký học tập -->
        <section class="muc-noi-dung" id="nhat-ky">
            <h2>Nhật ký học tập</h2>

            <p>
                Ghi lại những kiến thức và kinh nghiệm tôi học được
                trong quá trình thực hành lập trình.
            </p>

            <?php if ($thongBaoNhatKy !== ''): ?>
                <p role="status">
                    <?= e($thongBaoNhatKy) ?>
                </p>
            <?php endif; ?>

            <form method="post" action="gioithieu.php#nhat-ky">
                <input type="hidden" name="hanh_dong" value="them_nhat_ky">

                <p>
                    <label for="tieu_de_nhat_ky">Tiêu đề:</label>
                    <input
                        type="text"
                        id="tieu_de_nhat_ky"
                        name="tieu_de"
                        maxlength="200"
                        required
                    >
                </p>

                <p>
                    <label for="noi_dung_nhat_ky">Nội dung:</label>
                    <textarea
                        id="noi_dung_nhat_ky"
                        name="noi_dung"
                        rows="4"
                        required
                    ></textarea>
                </p>

                <button type="submit">Lưu nhật ký</button>
            </form>

            <h3>Các ghi chép gần đây</h3>

            <?php if (count($nhatKyHienThi) > 0): ?>
                <?php foreach ($nhatKyHienThi as $ghiChep): ?>
                    <article>
                        <h4><?= e($ghiChep['tieu_de']) ?></h4>
                        <p><?= nl2br(e($ghiChep['noi_dung'])) ?></p>
                        <p>
                            <small>
                                Ngày tạo: <?= e($ghiChep['ngay_tao']) ?>
                            </small>
                        </p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có ghi chép học tập nào.</p>
            <?php endif; ?>
                </section>

        <!-- 5. Danh sách công việc -->
        <section class="muc-noi-dung" id="cong-viec">
            <h2>Danh sách công việc</h2>

            <p>Ghi lại các công việc cần làm trong quá trình học tập.</p>

            <?php if ($thongBaoCongViec !== ''): ?>
                <p role="status"><?= e($thongBaoCongViec) ?></p>
            <?php endif; ?>

            <form method="post" action="gioithieu.php#cong-viec">
                <input type="hidden" name="hanh_dong" value="them_cong_viec">

                <p>
                    <label for="ten_cong_viec">Tên công việc:</label>
                    <input
                        type="text"
                        id="ten_cong_viec"
                        name="ten_cong_viec"
                        maxlength="200"
                        required
                    >
                </p>

                <button type="submit">Thêm công việc</button>
            </form>

            <h3>Các công việc của tôi</h3>

            <?php if (count($congViecHienThi) > 0): ?>
                <ul>
                    <?php foreach ($congViecHienThi as $congViec): ?>
                        <li>
                            <strong>
                                <?= e($congViec['ten_cong_viec']) ?>
                            </strong>

                            <?php if ((int) $congViec['hoan_thanh'] === 1): ?>
                                <span> — Đã hoàn thành</span>
                            <?php else: ?>
                                <span> — Chưa hoàn thành</span>

                                <form
                                    method="post"
                                    action="gioithieu.php#cong-viec"
                                >
                                    <input
                                        type="hidden"
                                        name="hanh_dong"
                                        value="hoan_thanh_cong_viec"
                                    >
                                    <input
                                        type="hidden"
                                        name="id_cong_viec"
                                        value="<?= e($congViec['id']) ?>"
                                    >
                                    <button type="submit">
                                        Đánh dấu hoàn thành
                                    </button>
                                </form>
                            <?php endif; ?>

                            <p>
                                <small>
                                    Ngày tạo: <?= e($congViec['ngay_tao']) ?>
                                </small>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Chưa có công việc nào. Hãy thêm công việc đầu tiên!</p>
            <?php endif; ?>
        </section>

        <!-- 6. Sở thích cá nhân -->
        <section class="muc-noi-dung">
            <h2>Sở thích cá nhân</h2>

            <ul>
                <li>Bóng đá</li>
                <li>Đọc sách</li>
                <li>Lập trình web</li>
            </ul>
        </section>

        <!-- 6. Chuyển giao diện sáng/tối -->
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
