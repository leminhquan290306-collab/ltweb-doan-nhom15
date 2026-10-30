<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/xac-thuc.php';

yeuCauQuyen('quan_tri');

use App\Data\KhoSanPham;

$khoSanPham = new KhoSanPham(db());
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: quan-tri.php?tab=san-pham');
    exit;
}

$sanPham = $khoSanPham->timTheoId($id);
if (!$sanPham) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$loiForm = [];
$danhMucList = db()->query("SELECT id, ten FROM danh_muc ORDER BY ten")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim((string)($_POST['ten'] ?? ''));
    $danhMucId = filter_input(INPUT_POST, 'danh_muc_id', FILTER_VALIDATE_INT);
    $gia = filter_input(INPUT_POST, 'gia', FILTER_VALIDATE_INT);
    $tonKho = filter_input(INPUT_POST, 'ton_kho', FILTER_VALIDATE_INT);
    $moTa = trim((string)($_POST['mo_ta'] ?? ''));

    if ($ten === '') $loiForm[] = 'Tên sản phẩm không được để trống.';
    if (!$danhMucId) $loiForm[] = 'Danh mục không hợp lệ.';
    if ($gia === false || $gia < 1000) $loiForm[] = 'Giá không hợp lệ (tối thiểu 1.000 VNĐ).';
    if ($tonKho === false || $tonKho < 0) $loiForm[] = 'Tồn kho không hợp lệ.';

    $anhUrl = $sanPham->getHinhAnh();
    if (isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
        $thuMucUpload = __DIR__ . '/images';
        if (!is_dir($thuMucUpload)) {
            mkdir($thuMucUpload, 0755, true);
        }
        $ext = pathinfo($_FILES['anh']['name'], PATHINFO_EXTENSION);
        $tenFile = 'sp_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file($_FILES['anh']['tmp_name'], $thuMucUpload . '/' . $tenFile)) {
            $anhUrl = 'images/' . $tenFile;
        }
    }

    if (empty($loiForm)) {
        try {
            $pdo = db();
            $stmt = $pdo->prepare("UPDATE san_pham SET danh_muc_id = ?, ten = ?, gia = ?, ton_kho = ?, anh = ?, mo_ta = ? WHERE id = ?");
            $stmt->execute([$danhMucId, $ten, $gia, $tonKho, $anhUrl, $moTa, $id]);

            $_SESSION['quan_tri_tb'] = 'Cập nhật sản phẩm thành công!';
            header('Location: quan-tri.php?tab=san-pham');
            exit;
        } catch (PDOException $e) {
            $loiForm[] = 'Lỗi CSDL: ' . $e->getMessage();
        }
    }
}

$tieuDeTrang = 'Sửa sản phẩm';
$baseUrl = '';
require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 650px; margin: 30px auto; padding: 0 15px;">
    <section style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2>Sửa thông tin sản phẩm</h2>
        <p><a href="quan-tri.php?tab=san-pham">← Quay lại danh sách quản trị</a></p>

        <?php if (!empty($loiForm)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($loiForm as $loi): ?>
                        <li><?= e($loi) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="sua-san-pham.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <p style="margin-bottom: 15px;">
                <label for="ten">Tên sản phẩm:</label><br>
                <input type="text" id="ten" name="ten" value="<?= e($_POST['ten'] ?? $sanPham->getTen()) ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="danh_muc_id">Danh mục:</label><br>
                <select id="danh_muc_id" name="danh_muc_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    <!-- Thêm option rỗng có value="" để pass hoàn toàn lỗi W3C Validator -->
                    <option value="">-- Chọn danh mục sản phẩm --</option>
                    <?php 
                    $stmtDm = db()->prepare("SELECT danh_muc_id FROM san_pham WHERE id = ?");
                    $stmtDm->execute([$id]);
                    $dmHienTaiId = $stmtDm->fetchColumn();
                    foreach ($danhMucList as $dm): 
                    ?>
                        <option value="<?= $dm['id'] ?>" <?= ((int)$dmHienTaiId === (int)$dm['id']) ? 'selected' : '' ?>>
                            <?= e($dm['ten']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p style="margin-bottom: 15px;">
                <label for="gia">Giá sản phẩm (VNĐ):</label><br>
                <input type="number" id="gia" name="gia" min="1000" step="500" value="<?= e((string)(int)$sanPham->getGia()) ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="ton_kho">Số lượng tồn kho:</label><br>
                <input type="number" id="ton_kho" name="ton_kho" min="0" value="<?= e((string)$sanPham->getSoLuong()) ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label>Ảnh hiện tại:</label><br>
                <img src="<?= e($sanPham->getHinhAnh()) ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; margin-top: 5px; border-radius: 4px;"><br>
                <label for="anh" style="margin-top: 10px; display: block;">Thay đổi ảnh mới (không bắt buộc):</label>
                <input type="file" id="anh" name="anh" accept="image/*">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="mo_ta">Mô tả sản phẩm:</label><br>
                <textarea id="mo_ta" name="mo_ta" rows="4" style="width: 100%; padding: 8px; margin-top: 5px;"><?= e($sanPham->getMoTa()) ?></textarea>
            </p>

            <button type="submit" style="background: #f39c12; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                Cập nhật sản phẩm
            </button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>