<?php

require_once __DIR__ . '/inc/config.php';

use App\Services\GioHang;

$tieuDeTrang = 'Giỏ hàng - Quà Tặng Thông Minh';
$baseUrl = '';

$gioHang = new GioHang();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!kiemTraCsrfToken(
        is_string($csrfToken) ? $csrfToken : null
    )) {
        http_response_code(419);
        exit('419 - CSRF token không hợp lệ.');
    }

    $hanhDong = trim(
        (string) ($_POST['hanh-dong'] ?? '')
    );

    try {

        if ($hanhDong === 'them') {

            $id = filter_input(
                INPUT_POST,
                'id',
                FILTER_VALIDATE_INT
            );

            $soLuong = filter_input(
                INPUT_POST,
                'so-luong',
                FILTER_VALIDATE_INT
            );

            if (
                $id === false ||
                $id === null ||
                $id < 1 ||
                $soLuong === false ||
                $soLuong === null ||
                $soLuong < 1
            ) {
                throw new InvalidArgumentException(
                    'Thông tin sản phẩm không hợp lệ.'
                );
            }

            $gioHang->them($id, $soLuong);

        } elseif ($hanhDong === 'cap-nhat') {

            $id = filter_input(
                INPUT_POST,
                'id',
                FILTER_VALIDATE_INT
            );

            $soLuong = filter_input(
                INPUT_POST,
                'so-luong',
                FILTER_VALIDATE_INT
            );

            if (
                $id === false ||
                $id === null ||
                $id < 1 ||
                $soLuong === false ||
                $soLuong === null
            ) {
                throw new InvalidArgumentException(
                    'Thông tin cập nhật không hợp lệ.'
                );
            }

            $gioHang->capNhat($id, $soLuong);

        } elseif ($hanhDong === 'xoa') {

            $id = filter_input(
                INPUT_POST,
                'id',
                FILTER_VALIDATE_INT
            );

            if (
                $id === false ||
                $id === null ||
                $id < 1
            ) {
                throw new InvalidArgumentException(
                    'ID sản phẩm không hợp lệ.'
                );
            }

            $gioHang->xoa($id);

        } elseif ($hanhDong === 'xoa-tat-ca') {

            $gioHang->xoaTatCa();

        } else {

            throw new InvalidArgumentException(
                'Hành động không hợp lệ.'
            );
        }

        /*
         * PRG: POST -> Redirect -> GET
         */
        redirect('gio-hang.php');

    } catch (Throwable $e) {

        $_SESSION['gio_hang_loi'] = $e->getMessage();

        redirect('gio-hang.php');
    }
}

$loi = $_SESSION['gio_hang_loi'] ?? null;
unset($_SESSION['gio_hang_loi']);

$danhSach = $gioHang->laySanPham();
$tongTien = $gioHang->tongTien();
$tongSoLuong = $gioHang->tongSoLuong();

require_once __DIR__ . '/inc/header.php';
?>

<main>

    <section class="card">

        <h2 class="card__title">
            Giỏ hàng
        </h2>

        <?php if ($loi !== null): ?>

            <p role="alert">
                <?= e($loi) ?>
            </p>

        <?php endif; ?>

        <?php if ($danhSach === []): ?>

            <p>
                Giỏ hàng hiện đang trống.
            </p>

            <p>
                <a
                    class="btn btn--chinh"
                    href="danh-sach.php">
                    Tiếp tục mua sắm
                </a>
            </p>

        <?php else: ?>

            <p>
                Tổng số sản phẩm:
                <strong><?= e($tongSoLuong) ?></strong>
            </p>

            <div class="luoi-san-pham">

                <?php foreach ($danhSach as $item): ?>

                    <?php
                    $sanPham = $item['sanPham'];
                    $soLuong = $item['soLuong'];
                    $thanhTien = $item['thanhTien'];
                    ?>

                    <article class="card">

                        <figure>

                            <img
                                src="<?= e($sanPham->getHinhAnh()) ?>"
                                alt="<?= e($sanPham->getTen()) ?>"
                                class="anh-responsive">

                        </figure>

                        <h3 class="card__title">
                            <?= e($sanPham->getTen()) ?>
                        </h3>

                        <p>
                            Đơn giá:
                            <strong>
                                <?= e(number_format(
                                    $sanPham->getGia(),
                                    0,
                                    ',',
                                    '.'
                                )) ?> đ
                            </strong>
                        </p>

                        <p>
                            Thành tiền:
                            <strong>
                                <?= e(number_format(
                                    $thanhTien,
                                    0,
                                    ',',
                                    '.'
                                )) ?> đ
                            </strong>
                        </p>

                        <form
                            method="POST"
                            action="gio-hang.php">

                            <input
                                type="hidden"
                                name="hanh-dong"
                                value="cap-nhat">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= e($sanPham->getId()) ?>">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(taoCsrfToken()) ?>">

                            <label
                                for="so-luong-<?= e($sanPham->getId()) ?>">
                                Số lượng:
                            </label>

                            <input
                                type="number"
                                id="so-luong-<?= e($sanPham->getId()) ?>"
                                name="so-luong"
                                min="1"
                                max="<?= e($sanPham->getSoLuong()) ?>"
                                value="<?= e($soLuong) ?>"
                                required>

                            <button type="submit">
                                Cập nhật
                            </button>

                        </form>

                        <form
                            method="POST"
                            action="gio-hang.php">

                            <input
                                type="hidden"
                                name="hanh-dong"
                                value="xoa">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= e($sanPham->getId()) ?>">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(taoCsrfToken()) ?>">

                            <button type="submit">
                                Xóa sản phẩm
                            </button>

                        </form>

                    </article>

                <?php endforeach; ?>

            </div>

            <section class="card">

                <h3>
                    Tổng cộng
                </h3>

                <p>
                    <strong>
                        <?= e(number_format(
                            $tongTien,
                            0,
                            ',',
                            '.'
                        )) ?> đ
                    </strong>
                </p>

                <form
                    method="POST"
                    action="gio-hang.php">

                    <input
                        type="hidden"
                        name="hanh-dong"
                        value="xoa-tat-ca">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(taoCsrfToken()) ?>">

                    <button type="submit">
                        Xóa tất cả
                    </button>

                </form>

            </section>

        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
