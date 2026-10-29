<?php

require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;
use App\Services\GioHang;

$tieuDeTrang = 'Danh sách quà tặng - Quà Tặng Thông Minh';
$baseUrl = '';

$khoSanPham = new KhoSanPham();
$gioHang = new GioHang();
$tongSoLuongGioHang = $gioHang->tongSoLuong();

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

                        <p style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                            <a
                                class="btn btn--chinh"
                                href="chi-tiet.php?id=<?= e($sanPham->getId()) ?>">
                                Xem chi tiết
                            </a>

                            <!-- Form xử lý thêm vào giỏ hàng bằng AJAX -->
                            <form class="form-them-gio" method="POST" action="gio-hang.php" style="margin: 0;">
                                <input type="hidden" name="ajax" value="1">
                                <input type="hidden" name="hanh-dong" value="them">
                                <input type="hidden" name="id" value="<?= e($sanPham->getId()) ?>">
                                <input type="hidden" name="so-luong" value="1">
                                <input type="hidden" name="csrf_token" value="<?= e(taoCsrfToken()) ?>">
                                <button type="submit" style="background: #27ae60; color: #fff; border: none; padding: 10px 14px; border-radius: 4px; cursor: pointer; font-weight: 500;">
                                    🛒 Thêm vào giỏ
                                </button>
                            </form>
                        </p>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <!-- BONG BÓNG GIỎ HÀNG NỔI (FLOATING CART BUBBLE) -->
        <div id="bong-bong-gio-hang" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999;">
            <a href="gio-hang.php" style="background: #2c3e50; color: #fff; width: 65px; height: 65px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.3); font-size: 26px; position: relative; transition: transform 0.2s;">
                🛒
                <span id="so-luong-bong-bong" style="position: absolute; top: -5px; right: -5px; background: #e74c3c; color: #fff; font-size: 14px; font-weight: bold; width: 26px; height: 26px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff;">
                    <?= e($tongSoLuongGioHang) ?>
                </span>
            </a>
        </div>

        <!-- SCRIPT XỬ LÝ AJAX THÊM VÀO GIỎ HÀNG KHÔNG RELOAD TRANG -->
        <script>
            document.querySelectorAll('.form-them-gio').forEach(form => {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    
                    try {
                        const response = await fetch('gio-hang.php', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            const badge = document.getElementById('so-luong-bong-bong');
                            if (badge) {
                                badge.textContent = result.tongSoLuong;
                            }
                            
                            const bubble = document.querySelector('#bong-bong-gio-hang a');
                            if (bubble) {
                                bubble.style.transform = 'scale(1.2)';
                                setTimeout(() => {
                                    bubble.style.transform = 'scale(1)';
                                }, 200);
                            }
                        } else {
                            alert('Có lỗi xảy ra: ' + (result.message || 'Không thể thêm vào giỏ'));
                        }
                    } catch (error) {
                        console.error('Lỗi:', error);
                        this.submit();
                    }
                });
            });
        </script>

        <noscript>
            <p>
                JavaScript đang tắt. Chức năng tìm kiếm, lọc và
                sắp xếp vẫn hoạt động bằng PHP trên máy chủ.
            </p>
        </noscript>

    </section>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>