<?php

require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;

$tieuDeTrang = 'Danh sách quà tặng - Quà Tặng Thông Minh';
$baseUrl = '';

$khoSanPham = new KhoSanPham();

$tuKhoa = trim((string) ($_GET['tim-kiem'] ?? ''));
$danhMuc = trim((string) ($_GET['loc-danh-muc'] ?? ''));
$sapXep = trim((string) ($_GET['sap-xep'] ?? ''));

$sanPhams = $khoSanPham->layTatCa();

/*
 * Tìm kiếm theo tên sản phẩm.
 */
if ($tuKhoa !== '') {
    $tuKhoaTimKiem = mb_strtolower($tuKhoa, 'UTF-8');

    $sanPhams = array_filter(
        $sanPhams,
        function ($sanPham) use ($tuKhoaTimKiem): bool {
            return str_contains(
                mb_strtolower($sanPham->getTen(), 'UTF-8'),
                $tuKhoaTimKiem
            );
        }
    );
}

/*
 * Lọc theo danh mục.
 */
if ($danhMuc !== '') {
    $sanPhams = array_filter(
        $sanPhams,
        function ($sanPham) use ($danhMuc): bool {
            return $sanPham->getDanhMuc() === $danhMuc;
        }
    );
}

/*
 * Sắp xếp.
 */
switch ($sapXep) {
    case 'gia-tang':
        usort(
            $sanPhams,
            fn($a, $b) => $a->getGia() <=> $b->getGia()
        );
        break;

    case 'gia-giam':
        usort(
            $sanPhams,
            fn($a, $b) => $b->getGia() <=> $a->getGia()
        );
        break;

    case 'ten-tang':
        usort(
            $sanPhams,
            fn($a, $b) => strcasecmp($a->getTen(), $b->getTen())
        );
        break;

    case 'ten-giam':
        usort(
            $sanPhams,
            fn($a, $b) => strcasecmp($b->getTen(), $a->getTen())
        );
        break;
}

require_once __DIR__ . '/inc/header.php';
?>

<main>

    <section class="danh-sach-san-pham">

        <h2>Danh sách sản phẩm</h2>

        <form method="GET" action="danh-sach.php">

            <div class="bo-loc-san-pham">

                <p>
                    <label for="tim-kiem">
                        Tìm kiếm sản phẩm:
                    </label>

                    <input
                        type="search"
                        id="tim-kiem"
                        name="tim-kiem"
                        value="<?= e($tuKhoa) ?>"
                        placeholder="Nhập tên sản phẩm...">
                </p>

                <p>
                    <label for="loc-danh-muc">
                        Lọc theo danh mục:
                    </label>

                    <select
                        id="loc-danh-muc"
                        name="loc-danh-muc">

                        <option value="">
                            Tất cả danh mục
                        </option>

                        <option
                            value="banh-keo"
                            <?= $danhMuc === 'banh-keo' ? 'selected' : '' ?>>
                            Bánh kẹo
                        </option>

                        <option
                            value="sinh-nhat"
                            <?= $danhMuc === 'sinh-nhat' ? 'selected' : '' ?>>
                            Sinh nhật
                        </option>

                        <option
                            value="ban-be"
                            <?= $danhMuc === 'ban-be' ? 'selected' : '' ?>>
                            Bạn bè
                        </option>

                        <option
                            value="dip-le"
                            <?= $danhMuc === 'dip-le' ? 'selected' : '' ?>>
                            Dịp lễ
                        </option>

                        <option
                            value="cao-cap"
                            <?= $danhMuc === 'cao-cap' ? 'selected' : '' ?>>
                            Cao cấp
                        </option>

                        <option
                            value="gia-dinh"
                            <?= $danhMuc === 'gia-dinh' ? 'selected' : '' ?>>
                            Gia đình
                        </option>

                    </select>
                </p>

                <p>
                    <label for="sap-xep">
                        Sắp xếp:
                    </label>

                    <select
                        id="sap-xep"
                        name="sap-xep">

                        <option
                            value=""
                            <?= $sapXep === '' ? 'selected' : '' ?>>
                            Mặc định
                        </option>

                        <option
                            value="gia-tang"
                            <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>
                            Giá tăng dần
                        </option>

                        <option
                            value="gia-giam"
                            <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>
                            Giá giảm dần
                        </option>

                        <option
                            value="ten-tang"
                            <?= $sapXep === 'ten-tang' ? 'selected' : '' ?>>
                            Tên A-Z
                        </option>

                        <option
                            value="ten-giam"
                            <?= $sapXep === 'ten-giam' ? 'selected' : '' ?>>
                            Tên Z-A
                        </option>

                    </select>
                </p>

                <p>
                    <button type="submit">
                        Lọc sản phẩm
                    </button>

                    <a href="danh-sach.php">
                        Xóa bộ lọc
                    </a>
                </p>

            </div>

        </form>

        <p aria-live="polite">
            Tìm thấy
            <strong><?= count($sanPhams) ?></strong>
            sản phẩm.
        </p>

        <?php if ($sanPhams === []): ?>

            <p>
                Không tìm thấy sản phẩm phù hợp.
            </p>

        <?php else: ?>

            <div
                id="danh-sach-dong"
                class="luoi-san-pham">

                <?php foreach ($sanPhams as $sanPham): ?>

                    <article class="card">

                        <figure>

                            <img
                                src="<?= e($sanPham->getHinhAnh()) ?>"
                                alt="<?= e($sanPham->getTen()) ?>"
                                class="anh-responsive"
                                loading="lazy">

                        </figure>

                        <h3 class="card__title">
                            <?= e($sanPham->getTen()) ?>
                        </h3>

                        <p>
                            Danh mục:
                            <strong>
                                <?= e($sanPham->getDanhMuc()) ?>
                            </strong>
                        </p>

                        <p>
                            Giá:
                            <strong>
                                <?= e(number_format($sanPham->getGia(), 0, ',', '.')) ?> đ
                            </strong>
                        </p>

                        <p>
                            Số lượng:
                            <?= e($sanPham->getSoLuong()) ?>
                        </p>

                        <p>
                            <?= e($sanPham->getMoTa()) ?>
                        </p>

                        <p>
                            <a
                                class="btn btn--chinh"
                                href="chi-tiet.php?id=<?= e($sanPham->getId()) ?>">
                                Xem chi tiết
                            </a>
                        </p>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <noscript>
            <p>
                JavaScript đang tắt. Chức năng tìm kiếm, lọc và
                sắp xếp vẫn hoạt động bằng PHP trên máy chủ.
            </p>
        </noscript>

    </section>

    <section class="card">

        <h2 class="card__title">
            Khám phá sản phẩm
        </h2>

        <p>
            Danh sách trên cung cấp những sản phẩm quà tặng phổ biến,
            giúp người dùng tham khảo theo ngân sách và đối tượng nhận quà.
        </p>

        <p>
            <a
                class="btn btn--chinh"
                href="chi-tiet.php">
                Xem chi tiết sản phẩm tiêu biểu
            </a>
        </p>

        <p>
            <a
                class="btn btn--phu"
                href="lien-he.php">
                Đi đến trang liên hệ
            </a>
        </p>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
