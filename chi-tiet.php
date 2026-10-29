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
$sanPhamCungDanhMuc = [];

if ($id !== false && $id !== null && $id > 0) {
    $sanPham = $khoSanPham->timTheoId($id);
    
    // Bổ sung lấy 4 sản phẩm cùng danh mục theo đúng chuẩn yêu cầu Bảng 1[cite: 4]
    if ($sanPham !== null) {
        // Lấy ID danh mục (nếu bảng dùng danh_muc_id, ta truy vấn thông qua đối tượng hoặc database)
        // Dưới đây ta lấy trực tiếp qua PDO từ hàm hamCSDL() cho nhanh và khớp tuyệt đối
        $stmtDm = hamCSDL()->prepare("SELECT danh_muc_id FROM san_pham WHERE id = ?");
        $stmtDm->execute([$id]);
        $row = $stmtDm->fetch();
        if ($row) {
            $sanPhamCungDanhMuc = $khoSanPham->layCungDanhMuc((int)$row['danh_muc_id'], $id, 4);
        }
    }
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

                    <!-- Thêm liên kết dẫn nhanh tới giỏ hàng -->
                    <p style="margin-top: 15px;">
                        <a href="gio-hang.php" style="color: #3498db; text-decoration: underline; font-weight: bold;">
                            🛒 Xem giỏ hàng & Tiến hành đặt hàng ➔
                        </a>
                    </p>

                </section>

            </div>

        </article>

        <!-- KHỐI HIỂN THỊ 4 SẢN PHẨM CÙNG DANH MỤC (ĐÚNG YÊU CẦU BẢNG 1) -->
        <?php if (!empty($sanPhamCungDanhMuc)): ?>
            <section class="card" style="margin-top: 30px;">
                <h3>Sản phẩm cùng danh mục</h3>
                <div class="luoi-san-pham" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: 15px;">
                    <?php foreach ($sanPhamCungDanhMuc as $spCung): ?>
                        <div style="border: 1px solid #ddd; padding: 10px; border-radius: 6px; background: #fff;">
                            <img src="<?= e($spCung->getHinhAnh()) ?>" alt="<?= e($spCung->getTen()) ?>" style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px;">
                            <h4 style="font-size: 14px; margin: 10px 0 5px;"><?= e($spCung->getTen()) ?></h4>
                            <p style="color: #e74c3c; font-weight: bold; font-size: 13px;"><?= number_format($spCung->getGia(), 0, ',', '.') ?> đ</p>
                            <a href="chi-tiet.php?id=<?= e($spCung->getId()) ?>" style="display: inline-block; padding: 4px 8px; background: #3498db; color: #fff; font-size: 12px; text-decoration: none; border-radius: 3px;">Xem chi tiết</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

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