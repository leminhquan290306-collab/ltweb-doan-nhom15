<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';

$tieuDeTrang = 'Quà Tặng Thông Minh - Tư vấn và mua sắm quà tặng';
$baseUrl = '';

// Kết nối CSDL và truy vấn danh sách sản phẩm từ MySQL thông qua PDO
try {
    $pdo = hamCSDL();
    $stmt = $pdo->query("
        SELECT san_pham.*, danh_muc.ten AS ten_danh_muc 
        FROM san_pham 
        JOIN danh_muc ON san_pham.danh_muc_id = danh_muc.id
        ORDER BY san_pham.ngay_tao DESC
    ");
    $danhSachSanPham = $stmt->fetchAll();

    // Đọc cookie sản phẩm đã xem gần đây
    $danhSachDaXem = [];
    if (isset($_COOKIE['san_pham_da_xem'])) {
        $cookieIds = json_decode($_COOKIE['san_pham_da_xem'], true);
        if (is_array($cookieIds) && !empty($cookieIds)) {
            $cookieIds = array_slice($cookieIds, 0, 4);
            $inQuery = implode(',', array_fill(0, count($cookieIds), '?'));
            $stmtDaXem = $pdo->prepare("
                SELECT san_pham.*, danh_muc.ten AS ten_danh_muc 
                FROM san_pham 
                JOIN danh_muc ON san_pham.danh_muc_id = danh_muc.id
                WHERE san_pham.id IN ($inQuery)
            ");
            $stmtDaXem->execute($cookieIds);
            $danhSachDaXem = $stmtDaXem->fetchAll();
        }
    }

} catch (PDOException $e) {
    echo "<div style='background: #fee; color: #c00; padding: 20px; margin: 20px; border: 1px solid #fcc; font-family: sans-serif;'>";
    echo "<strong>Lỗi truy vấn CSDL:</strong> " . $e->getMessage();
    echo "</div>";
    $danhSachSanPham = [];
    $danhSachDaXem = [];
}

require_once __DIR__ . '/inc/header.php';
?>

<main>

    <section class="hero">
        <h2>Tìm món quà phù hợp</h2>
        <p>
            Bạn đang phân vân không biết nên tặng gì cho người thân,
            bạn bè hoặc đồng nghiệp? Website giúp bạn tìm kiếm và
            lựa chọn món quà dựa trên người nhận, dịp tặng và ngân sách.
        </p>
        <p>
            Thay vì mất nhiều thời gian tìm kiếm, bạn có thể sử dụng
            công cụ tư vấn để nhận được những gợi ý phù hợp.
        </p>
    </section>

    <section class="card">
        <h2 class="card__title">Những khó khăn khi chọn quà</h2>
        <ul>
            <li>Không biết món quà nào phù hợp với người nhận.</li>
            <li>Khó lựa chọn quà theo sở thích cá nhân.</li>
            <li>Mất nhiều thời gian tìm kiếm sản phẩm.</li>
            <li>Khó kiểm soát món quà trong phạm vi ngân sách.</li>
        </ul>
    </section>

    <section class="card">
        <h2 class="card__title">Quy trình lựa chọn quà</h2>
        <ol>
            <li>Nhập thông tin về người nhận và dịp tặng.</li>
            <li>Nhận các gợi ý quà phù hợp.</li>
            <li>Xem sản phẩm và lựa chọn món quà.</li>
        </ol>
    </section>

    <section class="card">
        <h2 class="card__title">Các nhóm quà phổ biến</h2>
        <ul>
            <li>Quà sinh nhật</li>
            <li>Quà kỷ niệm</li>
            <li>Quà dành cho gia đình</li>
            <li>Quà dành cho bạn bè</li>
            <li>Quà dành cho đồng nghiệp</li>
        </ul>
    </section>
    
    <!-- KHU VỰC DANH SÁCH SẢN PHẨM TỪ MYSQL CÓ THANH CUỘN GỌN GÀNG -->
    <section class="card">
        <h2 class="card__title">Danh sách sản phẩm từ MySQL</h2>

        <div class="product-scroll-container">
            <div class="product-grid">
                <?php if (empty($danhSachSanPham)): ?>
                    <p>Chưa có sản phẩm nào trong cơ sở dữ liệu.</p>
                <?php else: ?>
                    <?php foreach ($danhSachSanPham as $sp): ?>
                        <div class="product-item">
                            <div>
                                <img src="<?= htmlspecialchars($sp['anh'] ?? 'images/hop-qua.jpg') ?>" alt="<?= htmlspecialchars($sp['ten']) ?>">
                                <h3 title="<?= htmlspecialchars($sp['ten']) ?>"><?= htmlspecialchars($sp['ten']) ?></h3>
                                <p style="font-size: 13px; margin: 4px 0;">Danh mục: <strong><?= htmlspecialchars($sp['ten_danh_muc']) ?></strong></p>
                                <p style="color: #e74c3c; font-weight: bold; margin: 4px 0;">Giá: <?= number_format($sp['gia'], 0, ',', '.') ?> đ</p>
                            </div>
                            <div>
                                <p style="font-size: 13px; margin: 4px 0;">Tồn kho: <?= $sp['ton_kho'] > 0 ? $sp['ton_kho'] : '<span style="color:red">Hết hàng</span>' ?></p>
                                <a href="chi-tiet.php?id=<?= $sp['id'] ?>" style="display: block; text-align: center; padding: 6px 12px; background: #3498db; color: #fff; text-decoration: none; border-radius: 4px; margin-top: 10px;">Xem chi tiết</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- KHỐI HIỂN THỊ SẢN PHẨM ĐÃ XEM GẦN ĐÂY TỪ COOKIE -->
    <?php if (!empty($danhSachDaXem)): ?>
        <section class="card">
            <h2 class="card__title">Sản phẩm bạn đã xem gần đây</h2>

            <div class="product-grid" style="grid-template-columns: repeat(4, 1fr);">
                <?php foreach ($danhSachDaXem as $sp): ?>
                    <div class="product-item" style="background: #f9f9f9; padding: 12px;">
                        <div>
                            <img src="<?= htmlspecialchars($sp['anh'] ?? 'images/hop-qua.jpg') ?>" alt="" style="height: 90px;">
                            <h3 title="<?= htmlspecialchars($sp['ten']) ?>" style="margin: 0 0 6px 0; font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($sp['ten']) ?></h3>
                            <p style="color: #e74c3c; font-weight: bold; font-size: 13px; margin: 0;"><?= number_format($sp['gia'], 0, ',', '.') ?> đ</p>
                        </div>
                        <div style="margin-top: 10px;">
                            <a href="chi-tiet.php?id=<?= $sp['id'] ?>" style="font-size: 12px; color: #3498db; text-decoration: underline;">Xem lại</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <section class="card">
        <h2 class="card__title">Khám phá website</h2>
        <figure>
            <img
                src="images/hop-qua.jpg"
                alt="Hộp quà được chuẩn bị để tặng"
                width="400"
                height="300"
                class="anh-responsive"
                loading="lazy">
            <figcaption>
                Gợi ý quà tặng được lựa chọn theo nhu cầu của người dùng.
            </figcaption>
        </figure>
        <p>
            <a
                class="btn btn--chinh"
                href="lien-he.php">
                Bắt đầu liên hệ
            </a>
        </p>
    </section>

    <section class="card" id="thoi-tiet">
        <h2 class="card__title">Thời tiết Đà Nẵng</h2>
        <p id="thong-bao-thoi-tiet" aria-live="polite">Đang tải dữ liệu thời tiết...</p>
        <div id="du-lieu-thoi-tiet">
            <p>Nhiệt độ: <strong id="nhiet-do">--</strong></p>
            <p>Độ ẩm: <strong id="do-am">--</strong></p>
            <p>Trạng thái: <strong id="trang-thai-thoi-tiet">--</strong></p>
        </div>
        <h3>Public REST API sử dụng</h3>
        <p>
            API:
            <code id="url-api-thoi-tiet">https://api.open-meteo.com/v1/forecast</code>
        </p>
        <p>Phản hồi JSON rút gọn:</p>
        <pre id="json-thoi-tiet" aria-live="polite">Đang tải...</pre>
    </section>

    <noscript>
        <p>
            JavaScript đang bị tắt. Nội dung giới thiệu website
            vẫn có thể được xem, nhưng dữ liệu thời tiết trực tuyến
            không được cập nhật.
        </p>
    </noscript>

</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>