<?php

require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;

$tieuDeTrang = 'Chi tiết sản phẩm - Quà Tặng Thông Minh';
$baseUrl = '';

$khoSanPham = new KhoSanPham();

/*
 * Lấy ID sản phẩm từ URL.
 */
$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$sanPham = null;

if ($id !== false && $id !== null && $id > 0) {
    $sanPham = $khoSanPham->timTheoId($id);
}

/*
 * Ghi sản phẩm vừa xem vào cookie.
 * Chỉ lưu tối đa 5 ID gần nhất.
 */
if ($sanPham !== null) {
    $sanPhamId = (string) $sanPham->getId();

    $sanPhamDaXem = [];

    if (isset($_COOKIE['san_pham_da_xem'])) {
        $duLieuCookie = json_decode(
            $_COOKIE['san_pham_da_xem'],
            true
        );

        if (is_array($duLieuCookie)) {
            foreach ($duLieuCookie as $item) {
                if (
                    is_int($item)
                    || (is_string($item) && ctype_digit($item))
                ) {
                    $sanPhamDaXem[] = (int) $item;
                }
            }
        }
    }

    $sanPhamDaXem = array_values(
        array_filter(
            $sanPhamDaXem,
            fn(int $item): bool => $item !== $sanPham->getId()
        )
    );

    array_unshift(
        $sanPhamDaXem,
        $sanPham->getId()
    );

    $sanPhamDaXem = array_slice(
        array_unique($sanPhamDaXem),
        0,
        5
    );

    setcookie(
        'san_pham_da_xem',
        json_encode($sanPhamDaXem),
        [
            'expires' => time() + (60 * 60 * 24 * 30),
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
}

require_once __DIR__ . '/inc/header.php';
?>

<main class="noi-dung-chi-tiet">

    <?php if ($sanPham === null): ?>

        <section class="card">

            <h2>Không tìm thấy sản phẩm</h2>

            <p>
                Sản phẩm bạn yêu cầu không tồn tại hoặc mã sản phẩm
                không hợp lệ.
            </p>

            <p>
                <a
                    class="btn btn--chinh"
                    href="danh-sach.php">
                    Quay lại danh sách sản phẩm
                </a>
            </p>

        </section>

    <?php else: ?>

        <article class="chi-tiet-chinh">

            <p id="thong-bao-chi-tiet" aria-live="polite">
                Đang hiển thị thông tin sản phẩm.
            </p>

            <div id="noi-dung-san-pham">

                <h2 id="ten-san-pham">
                    <?= e($sanPham->getTen()) ?>
                </h2>

                <button
                    type="button"
                    id="nut-yeu-thich-chi-tiet"
                    data-hanh-dong-yeu-thich="them"
                    data-id="<?= e($sanPham->getId()) ?>">
                    Thêm yêu thích
                </button>

                <figure>

                    <img
                        id="hinh-san-pham"
                        src="<?= e($sanPham->getHinhAnh()) ?>"
                        alt="<?= e($sanPham->getTen()) ?>"
                        width="400"
                        height="300"
                        class="anh-responsive">

                    <figcaption id="mo-ta-hinh">
                        Hình ảnh sản phẩm:
                        <?= e($sanPham->getTen()) ?>
                    </figcaption>

                </figure>

                <section>

                    <h3>
                        Giới thiệu sản phẩm
                    </h3>

                    <p id="mo-ta-san-pham">
                        <?= e($sanPham->getMoTa()) ?>
                    </p>

                </section>

                <section>

                    <h3>
                        Thông số sản phẩm
                    </h3>

                    <table>

                        <caption>
                            Thông tin chi tiết sản phẩm
                        </caption>

                        <thead>
                            <tr>
                                <th scope="col">
                                    Thông tin
                                </th>

                                <th scope="col">
                                    Chi tiết
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <th scope="row">
                                    Tên sản phẩm
                                </th>

                                <td id="thong-so-ten">
                                    <?= e($sanPham->getTen()) ?>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    Danh mục
                                </th>

                                <td id="thong-so-danh-muc">
                                    <?= e($sanPham->getDanhMuc()) ?>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    Giá tham khảo
                                </th>

                                <td id="thong-so-gia">
                                    <?= e(number_format(
                                        $sanPham->getGia(),
                                        0,
                                        ',',
                                        '.'
                                    )) ?> đ
                                </td>
                            </tr>

                            <tr>
                                <th scope="row">
                                    Số lượng
                                </th>

                                <td id="thong-so-so-luong">
                                    <?= e($sanPham->getSoLuong()) ?>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </section>

                <section>

                    <h3>
                        Gợi ý sử dụng
                    </h3>

                    <ul>
                        <li>
                            Phù hợp làm quà tặng trong nhiều dịp.
                        </li>

                        <li>
                            Có thể tặng bạn bè hoặc người thân.
                        </li>

                        <li>
                            Có thể kết hợp với thiệp chúc mừng.
                        </li>
                    </ul>

                </section>

                <section>

                    <h3>
                        Thêm vào giỏ hàng
                    </h3>

                    <form
                        method="POST"
                        action="gio-hang.php">

                        <input
                            type="hidden"
                            name="hanh-dong"
                            value="them">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= e($sanPham->getId()) ?>">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e(taoCsrfToken()) ?>">

                        <label for="so-luong">
                            Số lượng:
                        </label>

                        <input
                            type="number"
                            id="so-luong"
                            name="so-luong"
                            min="1"
                            max="<?= e($sanPham->getSoLuong()) ?>"
                            value="1"
                            required>

                        <button type="submit">
                            Thêm vào giỏ hàng
                        </button>

                    </form>

                </section>

            </div>

        </article>

    <?php endif; ?>

    <section class="card chi-tiet-phu">

        <h2 class="card__title">
            Khám phá thêm
        </h2>

        <p>
            <a
                class="btn btn--chinh"
                href="danh-sach.php">
                Quay lại danh sách sản phẩm
            </a>
        </p>

        <p>
            <a
                class="btn btn--phu"
                href="lien-he.php">
                Liên hệ để được tư vấn quà tặng
            </a>
        </p>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
