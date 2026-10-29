<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/tai-khoan.php';

if (!laQuanTri()) {
    redirect('dang-nhap.php');
}

$loiForm = [];
$danhMucList = hamCSDL()->query("SELECT id, ten FROM danh_muc ORDER BY ten")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim((string)($_POST['ten'] ?? ''));
    $danhMucId = filter_input(INPUT_POST, 'danh_muc_id', FILTER_VALIDATE_INT);
    $gia = filter_input(INPUT_POST, 'gia', FILTER_VALIDATE_INT);
    $tonKho = filter_input(INPUT_POST, 'ton_kho', FILTER_VALIDATE_INT);
    $moTa = trim((string)($_POST['mo_ta'] ?? ''));

    if ($ten === '' || mb_strlen($ten) > 200) {
        $loiForm[] = 'Tên sản phẩm không được để trống và tối đa 200 ký tự.';
    }
    if ($danhMucId === false || $danhMucId === null) {
        $loiForm[] = 'Vui lòng chọn danh mục hợp lệ.';
    }
    if ($gia === false || $gia === null || $gia < 1000) {
        $loiForm[] = 'Giá sản phẩm phải là số nguyên và lớn hơn hoặc bằng 1.000 VNĐ.';
    }
    if ($tonKho === false || $tonKho === null || $tonKho < 0) {
        $loiForm[] = 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.';
    }

    // Xử lý upload ảnh sản phẩm
    $anhUrl = 'images/default.jpg';
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
            $pdo = hamCSDL();
            $stmt = $pdo->prepare("INSERT INTO san_pham (danh_muc_id, ten, gia, ton_kho, anh, mo_ta) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$danhMucId, $ten, $gia, $tonKho, $anhUrl, $moTa]);

            $_SESSION['quan_tri_tb'] = 'Thêm sản phẩm thành công!';
            redirect('quan-tri.php?tab=san-pham');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Lỗi trùng lặp dữ liệu (1062)
                $loiForm[] = 'Sản phẩm này đã tồn tại trong hệ thống.';
            } else {
                $loiForm[] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
            }
        }
    }
}

$tieuDeTrang = 'Thêm sản phẩm mới';
$baseUrl = '';
require_once __DIR__ . '/inc/header.php';
?>

<main class="container" style="max-width: 650px; margin: 30px auto; padding: 0 15px;">
    <section style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2>Thêm sản phẩm mới</h2>
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

        <form action="them-san-pham.php" method="POST" enctype="multipart/form-data">
            <p style="margin-bottom: 15px;">
                <label for="ten">Tên sản phẩm:</label><br>
                <input type="text" id="ten" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="danh_muc_id">Danh mục:</label><br>
                <select id="danh_muc_id" name="danh_muc_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($danhMucList as $dm): ?>
                        <option value="<?= $dm['id'] ?>" <?= (isset($_POST['danh_muc_id']) && (int)$_POST['danh_muc_id'] === (int)$dm['id']) ? 'selected' : '' ?>>
                            <?= e($dm['ten']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p style="margin-bottom: 15px;">
                <label for="gia">Giá sản phẩm (VNĐ):</label><br>
                <input type="number" id="gia" name="gia" min="1000" step="500" value="<?= e($_POST['gia'] ?? '') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="ton_kho">Số lượng tồn kho:</label><br>
                <input type="number" id="ton_kho" name="ton_kho" min="0" value="<?= e($_POST['ton_kho'] ?? '10') ?>" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="anh">Hình ảnh sản phẩm:</label><br>
                <input type="file" id="anh" name="anh" accept="image/*" style="margin-top: 5px;">
            </p>

            <p style="margin-bottom: 15px;">
                <label for="mo_ta">Mô tả sản phẩm:</label><br>
                <textarea id="mo_ta" name="mo_ta" rows="4" style="width: 100%; padding: 8px; margin-top: 5px;"><?= e($_POST['mo_ta'] ?? '') ?></textarea>
            </p>

            <button type="submit" style="background: #27ae60; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                Lưu sản phẩm
            </button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>