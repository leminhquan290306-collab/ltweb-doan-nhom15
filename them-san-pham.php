<?php
declare(strict_types=1);

require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/xac-thuc.php';

yeuCauQuyen('quan_tri');

$loiForm = [];
$danhMucList = db()->query("SELECT id, ten FROM danh_muc ORDER BY ten")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!csrfHopLe(
        is_string($_POST['csrf_token'] ?? null)
            ? $_POST['csrf_token']
            : null
    )) {
        http_response_code(403);
        exit('403 - Yêu cầu không hợp lệ.');
    }

    $ten = trim((string)($_POST['ten'] ?? ''));
    $danhMucId = filter_input(INPUT_POST, 'danh_muc_id', FILTER_VALIDATE_INT);
    $gia = filter_input(INPUT_POST, 'gia', FILTER_VALIDATE_INT);
    $tonKho = filter_input(INPUT_POST, 'ton_kho', FILTER_VALIDATE_INT);
    $moTa = trim((string)($_POST['mo_ta'] ?? ''));

    if ($ten === '' || mb_strlen($ten) > 200) {
        $loiForm[] = 'Tên sản phẩm không được để trống và tối đa 200 ký tự.';
    }
    if ($danhMucId === false || $danhMucId === null || $danhMucId <= 0) {
    $loiForm[] = 'Vui lòng chọn danh mục hợp lệ.';
} else {
    $stmtKiemTraDanhMuc = db()->prepare(
        'SELECT 1 FROM danh_muc WHERE id = ? LIMIT 1'
    );
    $stmtKiemTraDanhMuc->execute([$danhMucId]);

    if ($stmtKiemTraDanhMuc->fetchColumn() === false) {
        $loiForm[] = 'Danh mục không tồn tại trong hệ thống.';
    }
}
    if ($gia === false || $gia === null || $gia < 1000) {
        $loiForm[] = 'Giá sản phẩm phải là số nguyên và lớn hơn hoặc bằng 1.000 VNĐ.';
    }
    if ($tonKho === false || $tonKho === null || $tonKho < 0) {
        $loiForm[] = 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.';
    }

    // Xử lý upload ảnh sản phẩm
$anhUrl = 'images/default.jpg';

if (isset($_FILES['anh']) && $_FILES['anh']['error'] !== UPLOAD_ERR_NO_FILE) {
    $fileAnh = $_FILES['anh'];

    if ($fileAnh['error'] !== UPLOAD_ERR_OK) {
        $loiForm[] = 'Tải ảnh lên thất bại.';
    } elseif ($fileAnh['size'] > 5 * 1024 * 1024) {
        $loiForm[] = 'Dung lượng ảnh không được vượt quá 5 MB.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fileAnh['tmp_name']);

        $dinhDangChoPhep = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];

        if (!isset($dinhDangChoPhep[$mime])) {
            $loiForm[] = 'Ảnh chỉ được có định dạng JPG, PNG hoặc WEBP.';
        } else {
            $thuMucUpload = __DIR__ . '/images';

            if (
                !is_dir($thuMucUpload)
                && !mkdir($thuMucUpload, 0755, true)
                && !is_dir($thuMucUpload)
            ) {
                $loiForm[] = 'Không thể tạo thư mục lưu ảnh.';
            } else {
                try {
                    $ext = $dinhDangChoPhep[$mime];

                    $tenFile = 'sp_' . bin2hex(random_bytes(16)) . '.' . $ext;

                    if (move_uploaded_file(
                        $fileAnh['tmp_name'],
                        $thuMucUpload . '/' . $tenFile
                    )) {
                        $anhUrl = 'images/' . $tenFile;
                    } else {
                        $loiForm[] = 'Không thể lưu ảnh lên máy chủ.';
                    }
                } catch (Throwable $e) {
                    error_log('Lỗi upload ảnh sản phẩm: ' . $e->getMessage());
                    $loiForm[] = 'Không thể tải ảnh lên. Vui lòng thử lại.';
                }
            }
        }
    }
}

    if (empty($loiForm)) {
        try {
            $pdo = db();
            $stmt = $pdo->prepare("INSERT INTO san_pham (danh_muc_id, ten, gia, ton_kho, anh, mo_ta) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$danhMucId, $ten, $gia, $tonKho, $anhUrl, $moTa]);

            $_SESSION['quan_tri_tb'] = 'Thêm sản phẩm thành công!';
            header('Location: quan-tri.php?tab=san-pham');
            exit;
        } catch (PDOException $e) {
    error_log('Lỗi thêm sản phẩm: ' . $e->getMessage());

    if ($e->getCode() === '23000') {
        $loiForm[] = 'Không thể thêm sản phẩm do dữ liệu bị trùng hoặc vi phạm ràng buộc.';
    } else {
        $loiForm[] = 'Không thể thêm sản phẩm. Vui lòng thử lại sau.';
    }
}
    }
}

$tieuDeTrang = 'Thêm sản phẩm mới';
$baseUrl = '';
require_once __DIR__ . '/inc/header.php';
?>

<main class="container sua-san-pham-container">
    <section class="sua-san-pham-khung">
        <h2>Thêm sản phẩm mới</h2>
        <p><a href="quan-tri.php?tab=san-pham">← Quay lại danh sách quản trị</a></p>

        <?php if (!empty($loiForm)): ?>
            <div class="sua-san-pham-loi">
    <ul>
                    <?php foreach ($loiForm as $loi): ?>
                        <li><?= e($loi) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="sua-san-pham-form" action="them-san-pham.php" method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
            <p>
                <label for="ten">Tên sản phẩm:</label><br>
                <input type="text" id="ten" name="ten" value="<?= e($_POST['ten'] ?? '') ?>" required>
            </p>

            <p>
                <label for="danh_muc_id">Danh mục:</label><br>
                <select id="danh_muc_id" name="danh_muc_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($danhMucList as $dm): ?>
                        <option value="<?= $dm['id'] ?>" <?= (isset($_POST['danh_muc_id']) && (int)$_POST['danh_muc_id'] === (int)$dm['id']) ? 'selected' : '' ?>>
                            <?= e($dm['ten']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <label for="gia">Giá sản phẩm (VNĐ):</label><br>
                <input type="number" id="gia" name="gia" min="1000" step="500" value="<?= e($_POST['gia'] ?? '') ?>" required>
            </p>

            <p>
                <label for="ton_kho">Số lượng tồn kho:</label><br>
                <input type="number" id="ton_kho" name="ton_kho" min="0" value="<?= e($_POST['ton_kho'] ?? '10') ?>" required>
            </p>

            <p>
                <label for="anh">Hình ảnh sản phẩm:</label><br>
                <input type="file" id="anh" name="anh" accept="image/*" class="sua-san-pham-file">
            </p>

            <p>
                <label for="mo_ta">Mô tả sản phẩm:</label><br>
                <textarea id="mo_ta" name="mo_ta" rows="4"><?= e($_POST['mo_ta'] ?? '') ?></textarea>
            </p>

            <button type="submit" class="nut-them-san-pham">
                Lưu sản phẩm
            </button>
        </form>
    </section>
</main>

<?php require_once __DIR__ . '/inc/footer.php'; ?>