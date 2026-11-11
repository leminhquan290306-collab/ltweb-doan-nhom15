<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';

use App\Data\KhoSanPham;
use App\Services\GioHang;

$tieuDeTrang = 'Danh sách quà tặng - Quà Tặng Thông Minh';
$baseUrl = '';

$khoSanPham = new KhoSanPham(hamCSDL());
$gioHang = new GioHang();
$tongSoLuongGioHang = method_exists($gioHang, 'tongSoLuong') ? $gioHang->tongSoLuong() : 0;

$duong = ['options' => ['min_range' => 1]];
$loc = [
    'q' => trim((string) ($_GET['tim-kiem'] ?? '')),
    'dm' => filter_var($_GET['loc-danh-muc'] ?? '', FILTER_VALIDATE_INT, $duong),
    'giaToiDa' => filter_var($_GET['gia-toi-da'] ?? '', FILTER_VALIDATE_INT, $duong),
];

$sapXep = trim((string) ($_GET['sap-xep'] ?? 'moi'));

$moiTrang = 8;
$tongSoDong = $khoSanPham->dem($loc);
$tongSoTrang = max(1, (int) ceil($tongSoDong / $moiTrang));

$hienTai = filter_var($_GET['trang'] ?? 1, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'default' => 1]
]);
$hienTai = min($hienTai, $tongSoTrang);
$boQua = ($hienTai - 1) * $moiTrang;

$sanPhams = $khoSanPham->trang($loc, $sapXep, $moiTrang, $boQua);
$dsDanhMuc = hamCSDL()->query('SELECT id, ten FROM danh_muc ORDER BY ten')->fetchAll();

function lienKetTrang(int $so): string
{
    return '?' . http_build_query(array_merge($_GET, ['trang' => $so]));
}

$csrfToken = csrfToken();

require_once __DIR__ . '/inc/header.php';
?>

<main>
    <section class="danh-sach-san-pham">
        <h2>Danh sách sản phẩm</h2>

        <form method="GET" action="danh-sach.php">
            <div class="bo-loc-san-pham">
                <p>
                    <label for="tim-kiem">Tìm kiếm sản phẩm:</label>
                    <input type="search" id="tim-kiem" name="tim-kiem" value="<?= e($loc['q']) ?>" placeholder="Nhập tên sản phẩm...">
                </p>

                <p>
                    <label for="loc-danh-muc">Lọc theo danh mục:</label>
                    <select id="loc-danh-muc" name="loc-danh-muc">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($dsDanhMuc as $dm): ?>
                            <option value="<?= $dm['id'] ?>" <?= $loc['dm'] === (int)$dm['id'] ? 'selected' : '' ?>>
                                <?= e($dm['ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </p>

                <p>
                    <label for="sap-xep">Sắp xếp:</label>
                    <select id="sap-xep" name="sap-xep">
                        <option value="moi" <?= $sapXep === 'moi' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="gia-tang" <?= $sapXep === 'gia-tang' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="gia-giam" <?= $sapXep === 'gia-giam' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="ten-tang" <?= $sapXep === 'ten-tang' ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="ten-giam" <?= $sapXep === 'ten-giam' ? 'selected' : '' ?>>Tên Z-A</option>
                    </select>
                </p>

                <p>
                    <button type="submit">Lọc sản phẩm</button>
                    <a href="danh-sach.php">Xóa bộ lọc</a>
                </p>
            </div>
        </form>

        <p aria-live="polite">
            Tìm thấy <strong><?= $tongSoDong ?></strong> sản phẩm.
        </p>

        <?php if ($sanPhams === []): ?>
            <p>Không tìm thấy sản phẩm phù hợp.</p>
        <?php else: ?>
            <div id="danh-sach-dong" class="luoi-san-pham">
                <?php foreach ($sanPhams as $sanPham): ?>
                    <article class="card">
                        <figure>
                            <img src="<?= e($sanPham->getHinhAnh()) ?>" alt="<?= e($sanPham->getTen()) ?>" class="anh-responsive" loading="lazy">
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

                        <!-- Đã thay thẻ <p> thành <div> để chuẩn cú pháp HTML5, tránh lỗi W3C -->
                        <div class="danh-sach-thao-tac">
                            <a class="btn btn--chinh" href="chi-tiet.php?id=<?= e($sanPham->getId()) ?>">
                                Xem chi tiết
                            </a>

                            <form class="form-them-gio" method="POST" action="gio-hang.php">
                                <input type="hidden" name="ajax" value="1">
                                <input type="hidden" name="hanh-dong" value="them">
                                <input type="hidden" name="id" value="<?= e($sanPham->getId()) ?>">
                                <input type="hidden" name="so-luong" value="1">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <button type="submit" class="nut-them-gio">
                                    🛒 Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if ($tongSoTrang > 1): ?>
                <nav class="phan-trang-danh-sach" aria-label="Phân trang danh sách">
    <?php for ($i = 1; $i <= $tongSoTrang; $i++): ?>
        <a
            href="<?= lienKetTrang($i) ?>"
            class="<?= $i === $hienTai ? 'dang-chon' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</nav>
            <?php endif; ?>
        <?php endif; ?>

        <!-- BONG BÓNG GIỎ HÀNG NỔI (FLOATING CART BUBBLE) -->
        <div id="bong-bong-gio-hang" class="bong-bong-gio-hang">
            <a href="gio-hang.php">
    🛒
    <span id="so-luong-bong-bong" class="so-luong-bong-bong">
        <?= e($tongSoLuongGioHang) ?>
    </span>
</a>
        </div>

       <script src="js/danh-sach.js" defer></script>

        <noscript>
            <p>
                JavaScript đang tắt. Chức năng tìm kiếm, lọc và
                sắp xếp vẫn hoạt động bằng PHP trên máy chủ.
            </p>
        </noscript>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>